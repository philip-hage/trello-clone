<?php
$dbconn = $system['dbconn'];

$cssStyles[] = $system['address'] . 'assets/css/style.css';

if (!empty($system['scope'])) {
    if ($system['scope'] == 'boards') {
        $application['pageFolder'] = 'boards';
        if ($system['action'] == 'overview') {
            $application['pageTemplate'] = 'page.board.overview';
            $jsScripts[] = ['src' => $system['address'] . 'assets/js/boards.js'];
            $cssStyles[] = $system['address'] . 'assets/css/boards.css';

            $boardId = $system['id'];
            $board = $dbconn->prepare('SELECT b.boardId, b.boardName, bg.boardbackgroundUrl FROM boards b INNER JOIN boardbackgrounds bg ON b.boardBackground = bg.boardbackgroundId WHERE boardId = :boardId');
            $board->bindParam(':boardId', $boardId);
            $board->execute();
            $board = $board->fetch(PDO::FETCH_ASSOC);

            $sections = $dbconn->prepare('SELECT sectionId, sectionBoardId, sectionName, sectionOrder, sectionColor FROM sections WHERE sectionBoardId = :boardId AND sectionIsActive = 1 ORDER BY sectionOrder');
            $sections->bindParam(':boardId', $boardId);
            $sections->execute(); // Execute the prepared statement
            $sections = $sections->fetchAll(PDO::FETCH_ASSOC);
            session_start();
            $userId = $_SESSION['user']['userId'];
            session_write_close();

            $labels = $dbconn->prepare('SELECT labelId, labelName FROM labels WHERE labelOwnerId = :labelOwnerId AND labelIsActive = 1');
            $labels->bindParam(':labelOwnerId', $userId);
            $labels->execute();
            $labels = $labels->fetchAll(PDO::FETCH_ASSOC);

            $tasks = $dbconn->query('SELECT taskId, taskSectionId, taskName, taskEndTime, taskDescription, taskOrder FROM tasks WHERE taskIsActive = 1 ORDER BY taskOrder');
            $tasks = $tasks->fetchAll(PDO::FETCH_ASSOC);

            $taskLabels = $dbconn->query('SELECT thl.labelId, thl.taskId, l.labelName, l.labelOwnerId from taskhaslabels thl INNER JOIN labels l ON thl.labelId = l.labelId WHERE l.labelIsActive = 1');
            $taskLabels = $taskLabels->fetchAll(PDO::FETCH_ASSOC);

            $taskLabelsByOwner = $dbconn->prepare('SELECT labelId, labelName FROM labels WHERE labelOwnerId = :labelOwnerId AND labelIsActive = 1');
            $taskLabelsByOwner->bindParam(':labelOwnerId', $userId);
            $taskLabelsByOwner->execute();
            $taskLabelsByOwner = $taskLabelsByOwner->fetchAll(PDO::FETCH_ASSOC);
        }
        if ($system['action'] == 'create') {
            $application['pageTemplate'] = 'page.board.create';
        }
        if ($system['action'] == 'read') {
            $application['pageTemplate'] = 'page.board.read';
        }
        if ($system['action'] == 'update') {
            $application['pageTemplate'] = 'page.board.update';
        }
        if ($system['action'] == 'delete') {
            $application['pageTemplate'] = 'page.board.delete';
        }
    } elseif ($system['scope'] == 'auth') {
        $application['pageFolder'] = 'auth';
        if ($system['action'] == 'login') {
            $application['pageTemplate'] = 'page.auth.login';
            $jsScripts[] = ['src' => $system['address'] . 'assets/js/auth.js'];
        }
        if ($system['action'] == 'register') {
            $application['pageTemplate'] = 'page.auth.register';
            $jsScripts[] = ['src' => $system['address'] . 'assets/js/auth.js'];
        }
        if ($system['action'] == 'logout') {
            $application['pageTemplate'] = 'page.auth.logout';
            $jsScripts[] = ['src' => $system['address'] . 'assets/js/auth.js'];
        }
    } elseif ($system['scope'] == 'workspaces') {
        $application['pageFolder'] = 'workspaces';
        if ($system['action'] == 'overview') {
            $application['pageTemplate'] = 'page.workspace.overview';
            $jsScripts[] = ['src' => $system['address'] . 'assets/js/workspaces.js'];
            $cssStyles[] = $system['address'] . 'assets/css/workspaces.css';

            $userId = $system['id'];
            $workspaces = $dbconn->prepare('SELECT w.workspaceId, w.workspaceName, w.workspaceUserId FROM rights r INNER JOIN workspaces w ON r.rightWorkspaceId = w.workspaceId WHERE r.rightUserId = :workspaceUserId AND w.workspaceIsActive = 1');
            $workspaces->bindParam(':workspaceUserId', $userId);
            $workspaces->execute();
            $workspaces = $workspaces->fetchAll(PDO::FETCH_ASSOC);

            $users = $dbconn->prepare('SELECT userId, userName FROM users WHERE userIsActive = 1');
            $users->execute();
            $users = $users->fetchAll(PDO::FETCH_ASSOC);

            $boardBackgrounds = $dbconn->prepare('SELECT boardbackgroundId, boardbackgroundUrl FROM boardbackgrounds WHERE boardbackgroundIsActive = 1');
            $boardBackgrounds->execute();
            $boardBackgrounds = $boardBackgrounds->fetchAll(PDO::FETCH_ASSOC);

            $workspaceByUser = $dbconn->prepare('SELECT rightWorkspaceId FROM rights r WHERE r.rightUserId = :workspaceUserId');
            $workspaceByUser->bindParam(':workspaceUserId', $userId);
            $workspaceByUser->execute();
            $workspaceByUser = $workspaceByUser->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($workspaceByUser)) {
                $recentlyBoardsQuery = $dbconn->prepare('SELECT b.boardId, b.boardWorkspaceId, b.boardName, b.boardModifyDate, bg.boardbackgroundUrl 
                FROM boards b 
                INNER JOIN boardbackgrounds bg ON b.boardBackground = bg.boardbackgroundId 
                WHERE b.boardIsActive = 1 AND b.boardWorkspaceId IN (' . implode(',', array_fill(0, count($workspaceByUser), '?')) . ') 
                ORDER BY b.boardModifyDate DESC 
                LIMIT 4');

                // Bind parameters
                foreach ($workspaceByUser as $index => $boardWorkspace) {
                    $recentlyBoardsQuery->bindValue($index + 1, $boardWorkspace['rightWorkspaceId']);
                }
                $recentlyBoardsQuery->execute();
                $recentlyBoards = $recentlyBoardsQuery->fetchAll(PDO::FETCH_ASSOC);

            }
        }
    }
}
