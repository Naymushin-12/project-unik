document.addEventListener('DOMContentLoaded', function() {
    // Элементы для работы с группами
    const createGroupBtn = document.querySelector('.create-btn');
    const groupList = document.querySelector('.group-list');
    const addMemberBtn = document.querySelector('.add-member');
    const membersList = document.querySelector('.group-list:last-child');
    
    // Хранилище данных
    let groups = JSON.parse(localStorage.getItem('groups')) || [
        { id: 1, name: 'Название группы 1', members: ['user1@example.com', 'user2@example.com'] },
        { id: 2, name: 'Название группы 2', members: ['user3@example.com'] }
    ];
    
    let selectedGroupId = null;
    
    // Инициализация интерфейса
    function initGroups() {
        renderGroupsList();
        if (groups.length > 0) {
            selectGroup(groups[0].id);
        }
    }
    
    // Отображение списка групп
    function renderGroupsList() {
        groupList.innerHTML = '';
        groups.forEach(group => {
            const li = document.createElement('li');
            li.textContent = group.name;
            li.addEventListener('click', () => selectGroup(group.id));
            if (group.id === selectedGroupId) {
                li.style.backgroundColor = '#e6f7e6';
            }
            groupList.appendChild(li);
        });
    }
    
    // Выбор группы
    function selectGroup(groupId) {
        selectedGroupId = groupId;
        renderGroupsList();
        renderMembersList();
    }
    
    // Отображение списка участников
    function renderMembersList() {
        const group = groups.find(g => g.id === selectedGroupId);
        if (!group) return;
        
        document.querySelector('.group-section:last-child .group-title:nth-of-type(1)').textContent = group.name;
        
        membersList.innerHTML = '';
        group.members.forEach(member => {
            const memberItem = document.createElement('div');
            memberItem.className = 'member-item';
            
            const memberEmail = document.createElement('span');
            memberEmail.textContent = member;
            
            const removeBtn = document.createElement('span');
            removeBtn.className = 'remove-btn';
            removeBtn.textContent = '×';
            removeBtn.addEventListener('click', () => removeMember(member));
            
            memberItem.appendChild(memberEmail);
            memberItem.appendChild(removeBtn);
            membersList.appendChild(memberItem);
        });
        
        const addMember = document.createElement('div');
        addMember.className = 'add-member';
        addMember.textContent = '+ Добавить участника';
        addMember.addEventListener('click', addNewMember);
        membersList.appendChild(addMember);
    }
    
    // Создание новой группы
    createGroupBtn.addEventListener('click', () => {
        const groupName = prompt('Введите название новой группы:');
        if (groupName && groupName.trim() !== '') {
            const newGroup = {
                id: Date.now(),
                name: groupName.trim(),
                members: []
            };
            groups.push(newGroup);
            saveGroups();
            renderGroupsList();
            selectGroup(newGroup.id);
        }
    });
    
    // Добавление участника
    function addNewMember() {
        const email = prompt('Введите email участника:');
        if (email && email.trim() !== '') {
            const group = groups.find(g => g.id === selectedGroupId);
            if (group && !group.members.includes(email.trim())) {
                group.members.push(email.trim());
                saveGroups();
                renderMembersList();
            }
        }
    }
    
    // Удаление участника
    function removeMember(email) {
        const group = groups.find(g => g.id === selectedGroupId);
        if (group) {
            group.members = group.members.filter(m => m !== email);
            saveGroups();
            renderMembersList();
        }
    }
    
    // Сохранение групп в localStorage
    function saveGroups() {
        localStorage.setItem('groups', JSON.stringify(groups));
    }
    
    // Инициализация при загрузке
    initGroups();
});