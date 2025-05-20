document.addEventListener('DOMContentLoaded', async function() {
    // Элементы интерфейса
    const mainPollView = document.getElementById('main-poll-view');
    const createPollView = document.getElementById('create-poll-view');
    const takePollView = document.getElementById('take-poll-view');
    const createPollBtn = document.getElementById('create-poll-btn');
    const pollList = document.getElementById('poll-list');
    const questionsContainer = document.getElementById('questions-container');
    const pollQuestionsContainer = document.getElementById('poll-questions-container');
    const addQuestionBtn = document.getElementById('add-question-btn');
    const savePollBtn = document.getElementById('save-poll-btn');
    const cancelPollBtn = document.getElementById('cancel-poll-btn');
    const submitPollBtn = document.getElementById('submit-poll-btn');
    const backToPollsBtn = document.getElementById('back-to-polls-btn');
    const pollTitle = document.getElementById('poll-title');
    const pollViewTitle = document.getElementById('poll-view-title');

    // Текущий выбранный опрос
    let currentPollId = null;

    // Инициализация страницы
    async function init() {
        await renderPollList();
        
        // Обработчики событий
        createPollBtn.addEventListener('click', showCreatePollView);
        addQuestionBtn.addEventListener('click', addQuestion);
        savePollBtn.addEventListener('click', savePoll);
        cancelPollBtn.addEventListener('click', showMainView);
        submitPollBtn.addEventListener('click', submitPoll);
        backToPollsBtn.addEventListener('click', showMainView);
    }

    // Показать главный вид
    async function showMainView() {
        mainPollView.style.display = 'block';
        createPollView.style.display = 'none';
        takePollView.style.display = 'none';
        await renderPollList();
    }

    // Показать вид создания опроса
    function showCreatePollView() {
        mainPollView.style.display = 'none';
        createPollView.style.display = 'block';
        takePollView.style.display = 'none';
        
        // Очистка формы
        pollTitle.value = '';
        questionsContainer.innerHTML = '';
        addQuestion(); // Добавляем первый вопрос по умолчанию
    }

    // Показать вид прохождения опроса
    async function showTakePollView(pollId) {
        mainPollView.style.display = 'none';
        createPollView.style.display = 'none';
        takePollView.style.display = 'block';
        
        currentPollId = pollId;
        
        try {
            // Загружаем данные опроса
            const response = await fetch(`api/get_poll.php?id=${pollId}`);
            const poll = await response.json();
            
            if (!poll || !poll.success) {
                throw new Error('Ошибка загрузки опроса');
            }
            
            pollViewTitle.textContent = poll.data.title;
            pollViewTitle.dataset.pollId = pollId;
            pollQuestionsContainer.innerHTML = '';
            
            // Отображаем вопросы
            poll.data.questions.forEach((question, qIndex) => {
                const questionDiv = document.createElement('div');
                questionDiv.className = 'poll-question';
                
                const questionText = document.createElement('h3');
                questionText.textContent = `${qIndex + 1}. ${question.text}`;
                questionDiv.appendChild(questionText);
                
                const optionsDiv = document.createElement('div');
                optionsDiv.className = 'poll-options';
                
                // Фиксированные варианты ответов
                const answerOptions = [
                    "1 - Отлично",
                    "2 - Хорошо",
                    "3 - Не очень",
                    "4 - Затрудняюсь с ответом",
                    "5 - Другой ответ"
                ];
                
                answerOptions.forEach((option, index) => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'poll-option';
                    
                    const radio = document.createElement('input');
                    radio.type = 'radio';
                    radio.name = `question-${qIndex}`;
                    radio.id = `question-${qIndex}-option-${index}`;
                    radio.value = option;
                    
                    const label = document.createElement('label');
                    label.htmlFor = `question-${qIndex}-option-${index}`;
                    label.textContent = option;
                    
                    optionDiv.appendChild(radio);
                    optionDiv.appendChild(label);
                    
                    // Особый случай для "Другой ответ"
                    if (index === 4) {
                        const otherInput = document.createElement('input');
                        otherInput.type = 'text';
                        otherInput.className = 'other-answer-input';
                        otherInput.placeholder = 'Введите ваш ответ';
                        otherInput.style.display = 'none';
                        otherInput.dataset.questionIndex = qIndex;
                        
                        radio.addEventListener('change', function() {
                            otherInput.style.display = this.checked ? 'block' : 'none';
                        });
                        
                        optionDiv.appendChild(otherInput);
                    }
                    
                    optionsDiv.appendChild(optionDiv);
                });
                
                questionDiv.appendChild(optionsDiv);
                pollQuestionsContainer.appendChild(questionDiv);
            });
        } catch (error) {
            console.error('Ошибка:', error);
            alert('Не удалось загрузить опрос');
            showMainView();
        }
    }

    // Добавить вопрос при создании опроса
    function addQuestion() {
        const questionDiv = document.createElement('div');
        questionDiv.className = 'question-item';
        
        const questionInput = document.createElement('input');
        questionInput.type = 'text';
        questionInput.placeholder = 'Текст вопроса';
        questionInput.className = 'question-input';
        questionInput.required = true;
        
        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-question-btn';
        removeBtn.textContent = '×';
        removeBtn.addEventListener('click', function() {
            if (document.querySelectorAll('.question-item').length > 1) {
                questionDiv.remove();
            } else {
                alert('Опрос должен содержать хотя бы один вопрос!');
            }
        });
        
        questionDiv.appendChild(questionInput);
        questionDiv.appendChild(removeBtn);
        questionsContainer.appendChild(questionDiv);
    }

    // Сохранить новый опрос
    async function savePoll() {
        const title = pollTitle.value.trim();
        if (!title) {
            alert('Введите название опроса!');
            return;
        }
        
        const questions = [];
        const questionInputs = document.querySelectorAll('.question-input');
        
        for (const input of questionInputs) {
            const questionText = input.value.trim();
            if (!questionText) {
                alert('Все вопросы должны быть заполнены!');
                return;
            }
            questions.push(questionText);
        }
        
        try {
            const response = await fetch('api/save_poll.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title: title,
                    questions: questions
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Опрос успешно сохранен!');
                
                // Добавляем уведомление
                await addNotification({
                    type: 'new-poll',
                    message: `Вы создали новый опрос "${title}"`,
                    date: new Date().toLocaleString(),
                    read: false
                });
                
                showMainView();
            } else {
                throw new Error(result.error || 'Неизвестная ошибка');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            alert('Не удалось сохранить опрос: ' + error.message);
        }
    }

    // Отправить результаты опроса
    async function submitPoll() {
        const pollId = currentPollId;
        if (!pollId) {
            alert('Ошибка: не выбран опрос');
            return;
        }
        
        try {
            // Получаем вопросы опроса
            const pollResponse = await fetch(`api/get_poll.php?id=${pollId}`);
            const poll = await pollResponse.json();
            
            if (!poll || !poll.success) {
                throw new Error('Не удалось загрузить вопросы опроса');
            }
            
            const answers = [];
            let allAnswered = true;
            
            // Собираем ответы
            poll.data.questions.forEach((_, qIndex) => {
                const selectedOption = document.querySelector(`input[name="question-${qIndex}"]:checked`);
                
                if (!selectedOption) {
                    allAnswered = false;
                    return;
                }
                
                let answer = selectedOption.value;
                
                if (answer === "5 - Другой ответ") {
                    const otherInput = document.querySelector(`.other-answer-input[data-question-index="${qIndex}"]`);
                    answer = otherInput.value.trim() || answer;
                }
                
                answers.push({
                    question_id: poll.data.questions[qIndex].id,
                    answer: answer
                });
            });
            
            if (!allAnswered) {
                alert('Пожалуйста, ответьте на все вопросы!');
                return;
            }
            
            // Отправляем ответы
            const response = await fetch('api/save_answers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    poll_id: pollId,
                    answers: answers
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Спасибо за участие в опросе! Ваши ответы сохранены.');
                
                // Добавляем уведомление
                await addNotification({
                    type: 'poll-completed',
                    message: `Вы прошли опрос "${poll.data.title}"`,
                    date: new Date().toLocaleString(),
                    read: false
                });
                
                // Перенаправляем на страницу результатов
                window.location.href = 'rezultat.html';
            } else {
                throw new Error(result.error || 'Неизвестная ошибка');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            alert('Не удалось отправить ответы: ' + error.message);
        }
    }

    // Отобразить список опросов
    async function renderPollList() {
        try {
            const response = await fetch('api/get_polls.php');
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Не удалось загрузить опросы');
            }
            
            const polls = result.data;
            pollList.innerHTML = '';
            
            if (polls.length === 0) {
                const li = document.createElement('li');
                li.textContent = 'Нет доступных опросов';
                pollList.appendChild(li);
                return;
            }
            
            polls.forEach(poll => {
                const li = document.createElement('li');
                li.className = 'poll-list-item';
                
                const titleSpan = document.createElement('span');
                titleSpan.className = 'poll-title';
                titleSpan.textContent = poll.title;
                
                const dateSpan = document.createElement('span');
                dateSpan.className = 'poll-date';
                dateSpan.textContent = new Date(poll.created_at).toLocaleString();
                
                const takeBtn = document.createElement('button');
                takeBtn.className = 'take-poll-btn';
                takeBtn.textContent = 'Пройти опрос';
                takeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    showTakePollView(poll.id);
                });
                
                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'delete-poll-btn';
                deleteBtn.textContent = '×';
                deleteBtn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    if (confirm('Вы уверены, что хотите удалить этот опрос?')) {
                        try {
                            const response = await fetch(`api/delete_poll.php?id=${poll.id}`, {
                                method: 'DELETE'
                            });
                            const result = await response.json();
                            
                            if (result.success) {
                                await renderPollList();
                            } else {
                                throw new Error(result.error || 'Не удалось удалить опрос');
                            }
                        } catch (error) {
                            console.error('Ошибка:', error);
                            alert('Не удалось удалить опрос: ' + error.message);
                        }
                    }
                });
                
                li.appendChild(titleSpan);
                li.appendChild(dateSpan);
                li.appendChild(takeBtn);
                li.appendChild(deleteBtn);
                
                pollList.appendChild(li);
            });
        } catch (error) {
            console.error('Ошибка:', error);
            pollList.innerHTML = '<li>Ошибка загрузки опросов</li>';
        }
    }

    // Добавить уведомление
    async function addNotification(notification) {
        try {
            const response = await fetch('api/add_notification.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(notification)
            });
            
            const result = await response.json();
            
            if (!result.success) {
                console.error('Ошибка сохранения уведомления:', result.error);
            }
            
            // Обновляем бейдж уведомлений
            updateNotificationBadge();
        } catch (error) {
            console.error('Ошибка:', error);
        }
    }

    // Инициализация при загрузке
    init();
});