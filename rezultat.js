document.addEventListener('DOMContentLoaded', function() {
    const resultsContainer = document.getElementById('results-container');
    const pollResults = JSON.parse(localStorage.getItem('pollResults')) || {};
    const polls = JSON.parse(localStorage.getItem('polls')) || [];

    function renderResults() {
        resultsContainer.innerHTML = '';

        // Проверяем, есть ли вообще пройденные опросы
        const hasResults = Object.keys(pollResults).length > 0 && 
                         Object.values(pollResults).some(results => results.length > 0);

        if (!hasResults) {
            resultsContainer.innerHTML = `
                <div class="no-results-message">
                    <p>Вы не прошли ни одного опроса.</p>
                    <p>Чтобы пройти опрос, перейдите по <a href="opros.html">вкладке "Опросы"</a></p>
                </div>
            `;
            return;
        }

        for (const pollId in pollResults) {
            const poll = polls.find(p => p.id === parseInt(pollId));
            if (!poll || pollResults[pollId].length === 0) continue;

            const pollSection = document.createElement('div');
            pollSection.className = 'poll-result-section';
            
            const pollTitle = document.createElement('h2');
            pollTitle.textContent = poll.title;
            pollSection.appendChild(pollTitle);
            
            const resultsList = document.createElement('div');
            resultsList.className = 'results-list';
            
            pollResults[pollId].forEach((result, index) => {
                const resultItem = document.createElement('div');
                resultItem.className = 'result-item';
                
                const resultHeader = document.createElement('h3');
                resultHeader.textContent = `Прохождение #${index + 1} (${result.date})`;
                resultItem.appendChild(resultHeader);
                
                const answersList = document.createElement('ul');
                answersList.className = 'answers-list';
                
                poll.questions.forEach((question, qIndex) => {
                    const answerItem = document.createElement('li');
                    answerItem.className = 'answer-item';
                    
                    const questionText = document.createElement('span');
                    questionText.className = 'question-text';
                    questionText.textContent = `${qIndex + 1}. ${question}`;
                    
                    const answerText = document.createElement('span');
                    answerText.className = 'answer-text';
                    answerText.textContent = result.answers[qIndex] || 'Нет ответа';
                    
                    answerItem.appendChild(questionText);
                    answerItem.appendChild(answerText);
                    answersList.appendChild(answerItem);
                });
                
                resultItem.appendChild(answersList);
                resultsList.appendChild(resultItem);
            });
            
            pollSection.appendChild(resultsList);
            resultsContainer.appendChild(pollSection);
        }
    }

    renderResults();
});