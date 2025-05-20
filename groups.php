<?php
session_start();
require_once 'auth.php';
checkAuth();

// Обработка POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $groupId = $_POST['group_id'] ?? null;
        $email = $_POST['email'] ?? null;
        
        switch ($_POST['action']) {
            case 'create_group':
                $groupName = $_POST['group_name'] ?? '';
                if (!empty($groupName)) {
                    $groupId = createGroup($groupName, $_SESSION['user_id']);
                    if ($groupId) {
                        $_SESSION['success'] = 'Группа успешно создана';
                    } else {
                        $_SESSION['error'] = 'Ошибка при создании группы';
                    }
                }
                break;
                
            case 'add_member':
                if ($groupId && $email) {
                    if (addMemberToGroup($groupId, $email)) {
                        $_SESSION['success'] = 'Участник успешно добавлен';
                    } else {
                        $_SESSION['error'] = 'Ошибка при добавлении участника';
                    }
                }
                break;
                
            case 'remove_member':
                if ($groupId && $email) {
                    if (removeMemberFromGroup($groupId, $email)) {
                        $_SESSION['success'] = 'Участник успешно удалён';
                    } else {
                        $_SESSION['error'] = 'Ошибка при удалении участника';
                    }
                }
                break;
        }
        
        header("Location: groups.php");
        exit;
    }
}

// Получаем список групп пользователя
$groups = getGroupsByUser($_SESSION['user_id']);
$selectedGroupId = $_GET['group_id'] ?? ($groups[0]['id'] ?? null);
$members = $selectedGroupId ? getGroupMembers($selectedGroupId) : [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление группами</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="sidebar">
        <a href="main.php" class="sidebar-item">
            <span class="icon">🏠</span>
            Главная
        </a>
        <a href="yvedomleniya.php" class="sidebar-item">
            <span class="icon">🔔</span>
            Уведомления
        </a>
        <a href="groups.php" class="sidebar-item active">
            <span class="icon">👥</span>
            Группы
        </a>
        <a href="opros.php" class="sidebar-item">
            <span class="icon">📝</span>
            Опросы
        </a>
        <a href="rezultat.php" class="sidebar-item">
            <span class="icon">📊</span>
            Результаты
        </a>
        <a href="report.php" class="sidebar-item">
            <span class="icon">⚠️</span>
            Сообщить об ошибке
        </a>
    </div>

    <div class="content">
        <div class="top-icons">
            <span class="icon">👤</span>
            <span class="icon">⚙️</span>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= safeOutput($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= safeOutput($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="groups-container">
            <!-- Секция для создания и выбора групп -->
            <div class="group-section">
                <h2 class="group-title">Создать новую группу</h2>
                <form method="POST" action="groups.php">
                    <input type="hidden" name="action" value="create_group">
                    <input type="text" name="group_name" placeholder="Название группы" required class="form-input">
                    <button type="submit" class="create-btn">+ Создать группу</button>
                </form>
                
                <h3 class="group-title">Ваши группы:</h3>
                <ul class="group-list">
                    <?php foreach ($groups as $group): ?>
                        <li>
                            <a href="groups.php?group_id=<?= $group['id'] ?>">
                                <?= safeOutput($group['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    
                    <?php if (empty($groups)): ?>
                        <li>У вас пока нет групп</li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Секция для управления составом группы -->
            <div class="group-section">
                <?php if ($selectedGroupId): ?>
                    <h2 class="group-title"><?= safeOutput($groups[array_search($selectedGroupId, array_column($groups, 'id'))]['name']) ?></h2>
                    <h3 class="group-title">Состав команды:</h3>
                    <div class="group-list">
                        <?php foreach ($members as $email): ?>
                            <div class="member-item">
                                <span><?= safeOutput($email) ?></span>
                                <form method="POST" action="groups.php" style="display: inline;">
                                    <input type="hidden" name="action" value="remove_member">
                                    <input type="hidden" name="group_id" value="<?= $selectedGroupId ?>">
                                    <input type="hidden" name="email" value="<?= safeOutput($email) ?>">
                                    <button type="submit" class="remove-btn">×</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                        
                        <form method="POST" action="groups.php">
                            <input type="hidden" name="action" value="add_member">
                            <input type="hidden" name="group_id" value="<?= $selectedGroupId ?>">
                            <input type="email" name="email" placeholder="Email участника" required class="form-input">
                            <button type="submit" class="add-member">+ Добавить участника</button>
                        </form>
                    </div>
                <?php else: ?>
                    <h2 class="group-title">Выберите группу</h2>
                    <p>Выберите группу из списка слева для просмотра и редактирования.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>