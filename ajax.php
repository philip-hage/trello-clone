<?php

$system['docroot'] = $_SERVER['DOCUMENT_ROOT'] . '/';
include $system['docroot'] . 'core/global.init.php';

$dbconn = $system['dbconn'];

header('Content-Type: application/json');
$decodedParams = json_decode(file_get_contents('php://input'));
$response = array();


if (isset($decodedParams->scope) && !empty($decodedParams->scope)) {
    if ($decodedParams->scope == 'auth') {
        if (isset($decodedParams->action) && !empty($decodedParams->action)) {
            if ($decodedParams->action == 'register') {
                $name = $decodedParams->name;
                $email = $decodedParams->email;
                $password = $decodedParams->password;

                // Check if the email already exists
                $stmt = $dbconn->prepare('SELECT userId FROM users WHERE userEmail = :userEmail');
                $stmt->bindParam(':userEmail', $email);
                $stmt->execute();
                $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingUser) {
                    $response['status'] = '400';
                    $response['message'] = 'Email already exists. Please choose a different email.';
                    echo json_encode($response);
                    return;
                }

                // If email doesn't exist, proceed with user registration
                $userId = generateRandomString(4);
                $createDate = $var['timestamp'];

                $stmt = $dbconn->prepare('INSERT INTO users (userId, userName, userEmail, userPassword, userCreateDate) VALUES (:userId, :userName, :userEmail, :userPassword, :userCreateDate)');
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':userName', $name);
                $stmt->bindParam(':userEmail', $email);
                $stmt->bindParam(':userPassword', $password);
                $stmt->bindParam(':userCreateDate', $createDate);
                $stmt->execute();

                $stmt = $dbconn->prepare('SELECT userId, userName, userEmail, userPassword FROM users WHERE userId = :userId');
                $stmt->bindParam(':userId', $userId);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $response['status'] = '200';
                    $response['message'] = 'User registered successfully!';
                    $response['userId'] = $user['userId'];
                    session_start();
                    $_SESSION['user'] = $user;
                    session_write_close();
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'User could not be registered';
                    echo json_encode($response);
                    return;
                }
            }

            if ($decodedParams->action == 'login') {
                $email = $decodedParams->email;
                $password = $decodedParams->password;

                $stmt = $dbconn->prepare('SELECT userId, userName, userEmail, userPassword FROM users WHERE userEmail = :userEmail AND userPassword = :userPassword');
                $stmt->bindParam(':userEmail', $email);
                $stmt->bindParam(':userPassword', $password);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $response['status'] = '200';
                    session_start();
                    $_SESSION['user'] = $user;
                    session_write_close();
                    $response['userId'] = $user['userId'];
                } else {
                    $response['status'] = '400';
                    $response['message'] = 'Invalid email or password';
                    echo json_encode($response);
                    return;
                }
            }
        }
    }
}

if (isset($decodedParams->scope) && !empty($decodedParams->scope)) {
    if ($decodedParams->scope == 'boards') {
        if (isset($decodedParams->action) && !empty($decodedParams->action)) {

            if ($decodedParams->action == 'updateTaskOrder') {
                $taskId = $decodedParams->taskId;
                $sectionId = $decodedParams->sectionId;
                $taskOrder = $decodedParams->taskOrder;

                $stmt = $dbconn->prepare('UPDATE tasks SET taskOrder = :taskOrder, taskSectionId = :taskSectionId  WHERE taskId = :taskId');
                $stmt->bindParam(':taskOrder', $taskOrder);
                $stmt->bindParam(':taskId', $taskId);
                $stmt->bindParam(':taskSectionId', $sectionId);

                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Task order updated successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Task order could not be updated';
                    echo json_encode($response);
                    return;
                }
            }

            if ($decodedParams->action == 'addTask') {
                $sectionId = $decodedParams->sectionId;
                $taskName = $decodedParams->taskName;
                $taskDescription = $decodedParams->taskDescription;

                    if ($decodedParams->taskEndTime !== NULL) {
                        $taskEndTime = $decodedParams->taskEndTime;
                    } else {
                        $taskEndTime = null;
                    }
                $taskCreateDate = $var['timestamp'];

                $maxOrderStmt = $dbconn->prepare('SELECT MAX(taskOrder) as maxOrder FROM tasks WHERE taskSectionId = :taskSectionId');
                $maxOrderStmt->bindParam(':taskSectionId', $sectionId);
                $maxOrderStmt->execute();
                $maxOrderResult = $maxOrderStmt->fetch(PDO::FETCH_ASSOC);

                $taskOrder = ($maxOrderResult['maxOrder'] !== null) ? $maxOrderResult['maxOrder'] + 1 : 1;

                $taskId = generateRandomString(4);

                $stmt = $dbconn->prepare('INSERT INTO tasks (taskId, taskSectionId, taskName, taskEndTime, taskOrder, taskDescription, taskCreateDate) VALUES (:taskId, :taskSectionId, :taskName, :taskEndTime, :taskOrder, :taskDescription, :taskCreateDate)');
                $stmt->bindParam(':taskId', $taskId);
                $stmt->bindParam(':taskSectionId', $sectionId);
                $stmt->bindParam(':taskName', $taskName);
                $stmt->bindParam(':taskEndTime', $taskEndTime);
                $stmt->bindParam(':taskOrder', $taskOrder);
                $stmt->bindParam(':taskDescription', $taskDescription);
                $stmt->bindParam(':taskCreateDate', $taskCreateDate);

                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Task added successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Task could not be added';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'addSection') {
                $sectionId = generateRandomString(4);
                $sectionBoardId = $decodedParams->sectionBoardId;
                $sectionName = $decodedParams->sectionName;
                $sectionColor = $decodedParams->sectionColor;
                $sectionCreateDate = $var['timestamp'];

                $maxOrderStmt = $dbconn->prepare('SELECT MAX(sectionOrder) as maxOrder FROM sections WHERE sectionBoardId = :sectionBoardId');
                $maxOrderStmt->bindParam(':sectionBoardId', $sectionBoardId);
                $maxOrderStmt->execute();
                $maxOrderResult = $maxOrderStmt->fetch(PDO::FETCH_ASSOC);

                $sectionOrder = ($maxOrderResult['maxOrder'] !== null) ? $maxOrderResult['maxOrder'] + 1 : 1;

                $stmt = $dbconn->prepare('INSERT INTO sections (sectionId, sectionBoardId, sectionName, sectionColor, sectionOrder, sectionCreateDate) VALUES (:sectionId, :sectionBoardId, :sectionName, :sectionColor, :sectionOrder, :sectionCreateDate)');
                $stmt->bindParam(':sectionId', $sectionId);
                $stmt->bindParam(':sectionBoardId', $sectionBoardId);
                $stmt->bindParam(':sectionName', $sectionName);
                $stmt->bindParam(':sectionColor', $sectionColor);
                $stmt->bindParam(':sectionOrder', $sectionOrder);
                $stmt->bindParam(':sectionCreateDate', $sectionCreateDate);

                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Section added successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Section could not be added';
                    echo json_encode($response);
                    return;
                }
            }

            if ($decodedParams->action == 'deleteSection') {
                $sectionId = $decodedParams->sectionId;

                $stmt = $dbconn->prepare('UPDATE sections SET sectionIsActive = 0, sectionOrder = 0 WHERE sectionId = :sectionId');
                $stmt->bindParam(':sectionId', $sectionId);

                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Section deleted successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Section could not be deleted';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'getTask') {
                $taskId = $decodedParams->taskId;

                $stmt = $dbconn->prepare('SELECT taskId, taskName, taskEndTime, taskDescription FROM tasks WHERE taskId = :taskId');
                $stmt->bindParam(':taskId', $taskId);
                $stmt->execute();
                $task = $stmt->fetch(PDO::FETCH_ASSOC);

                $response['status'] = '200';
                $response['task'] = $task;
            }
            if ($decodedParams->action == 'updateTask') {
                $formData = $decodedParams->formData;

                $taskId = $decodedParams->taskId;

                $endTime = $formData->taskEndTime;
                $endtime = strtotime($endTime);

                $taskLabel = $formData->taskLabelInput;
                $labelCreateDate = $var['timestamp'];

                session_start();
                $userId = $_SESSION['user']['userId'];
                session_write_close();
                $labelId = generateRandomString(4);

                $stmt = $dbconn->prepare('UPDATE tasks SET taskName = :taskName, taskDescription = :taskDescription, taskEndTime = :taskEndTime WHERE taskId = :taskId');
                $stmt->bindParam(':taskName', $formData->taskName);
                $stmt->bindParam(':taskDescription', $formData->taskDescription);
                $stmt->bindParam(':taskEndTime', $endtime);
                $stmt->bindParam(':taskId', $taskId);

                if ($stmt->execute()) {
                    if ($taskLabel !== '') {
                        // Check if label already exists for the specific owner
                        $stmt = $dbconn->prepare('SELECT * FROM labels WHERE labelName = :labelName AND labelOwnerId = :labelOwnerId');
                        $stmt->bindParam(':labelName', $taskLabel);
                        $stmt->bindParam(':labelOwnerId', $userId);
                        $stmt->execute();
                        $existingLabel = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($existingLabel) {
                            // Label exists, add to taskhaslabels
                            $labelId = $existingLabel['labelId'];
                        } else {
                            // Label doesn't exist for the specific owner, add it to labels table
                            $stmt = $dbconn->prepare('INSERT INTO labels (labelId, labelName, labelOwnerId, labelCreateDate) VALUES (:labelId, :labelName, :labelOwnerId, :labelCreateDate)');
                            $stmt->bindParam(':labelId', $labelId);
                            $stmt->bindParam(':labelName', $taskLabel);
                            $stmt->bindParam(':labelOwnerId', $userId);
                            $stmt->bindParam(':labelCreateDate', $labelCreateDate);
                            $stmt->execute();
                        }

                        // Add to taskhaslabels
                        $stmt = $dbconn->prepare('INSERT INTO taskhaslabels (taskId, labelId) VALUES (:taskId, :labelId)');
                        $stmt->bindParam(':taskId', $taskId);
                        $stmt->bindParam(':labelId', $labelId);
                        if ($stmt->execute()) {
                            $response['status'] = '200';
                            $response['message'] = 'Label added successfully!';
                        } else {
                            $response['status'] = '500';
                            $response['message'] = 'Label could not be added';
                            echo json_encode($response);
                            return;
                        }
                        $response['status'] = '200';
                        $response['message'] = 'Task updated successfully!';
                    } else {
                        $response['status'] = '200';
                        $response['message'] = 'Task was updated!';
                    }

                    $stmt = $dbconn->prepare('UPDATE checklists set checklistIsChecked = 0 WHERE checklistTaskId = :taskId');
                    $stmt->bindParam(':taskId', $taskId);
                    if ($stmt->execute()) {
                        if (!empty($formData->checkboxListIds)) {
                            $stmt = $dbconn->prepare('UPDATE checklists SET checklistIsChecked = 1 WHERE checklistId = :checklistId');

                            $checkboxListIds = $formData->checkboxListIds;
                            foreach ($checkboxListIds as $checklistId) {
                                $stmt->bindParam(':checklistId', $checklistId);
                                if ($stmt->execute()) {
                                    $response['status'] = '200';
                                    $response['message'] = 'checklist updated successfully to 1!';
                                } else {
                                    $response['status'] = '500';
                                    $response['message'] = 'Task could not be updated';
                                    echo json_encode($response);
                                    return;
                                }
                            }
                        }
                        $response['status'] = '200';
                        $response['message'] = 'Task updated successfully!';
                    } else {
                        $response['status'] = '500';
                        $response['message'] = 'Task could not be updated';
                        echo json_encode($response);
                        return;
                    }

                    if ($stmt->execute()) {

                        $response['status'] = '200';
                        $response['message'] = 'checklist updated successfully to 0!';
                    } else {
                        $response['status'] = '500';
                        $response['message'] = 'Task could not be updated';
                        echo json_encode($response);
                        return;
                    }
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Task could not be updated';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'removeLabel') {
                $taskId = $decodedParams->taskId;
                $labelId = $decodedParams->labelId;

                $stmt = $dbconn->prepare('DELETE FROM taskhaslabels WHERE taskId = :taskId AND labelId = :labelId');
                $stmt->bindParam(':taskId', $taskId);
                $stmt->bindParam(':labelId', $labelId);

                if ($stmt->execute()) {
                    $stmt = $dbconn->prepare('DELETE FROM labels WHERE labelId = :labelId');
                    $stmt->bindParam(':labelId', $labelId);
                    if ($stmt->execute()) {
                        $response['status'] = '200';
                        $response['message'] = 'Label removed successfully!';
                    } else {
                        $response['status'] = '500';
                        $response['message'] = 'Label could not be removed';
                        echo json_encode($response);
                        return;
                    }
                    $response['status'] = '200';
                    $response['message'] = 'Label removed successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Label could not be removed';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'getCheckboxList') {
                $taskId = $decodedParams->taskId;

                $stmt = $dbconn->prepare('SELECT checklistId, checklistTaskId, checklistDescription, checklistIsChecked FROM checklists WHERE checklistTaskId = :taskId');
                $stmt->bindParam(':taskId', $taskId);

                if ($stmt->execute()) {
                    $checklist = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['status'] = '200';
                    $response['checklist'] = $checklist;
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Checklist could not be fetched';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'addCheckbox') {
                $checklistId = generateRandomString(4);
                $taskId = $decodedParams->taskId;
                $checklistDescription = $decodedParams->checkboxDescription;
                $checklistCreateDate = $var['timestamp'];

                $stmt = $dbconn->prepare('INSERT INTO checklists (checklistId, checklistTaskId, checklistDescription, checklistCreateDate) VALUES (:checklistId, :checklistTaskId, :checklistDescription, :checklistCreateDate)');
                $stmt->bindParam(':checklistId', $checklistId);
                $stmt->bindParam(':checklistTaskId', $taskId);
                $stmt->bindParam(':checklistDescription', $checklistDescription);
                $stmt->bindParam(':checklistCreateDate', $checklistCreateDate);

                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Checkbox added successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Checkbox could not be added';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'deleteTask') {
                $taskId = $decodedParams->taskId;

                $stmt = $dbconn->prepare('UPDATE tasks SET taskIsActive = 0 WHERE taskId = :taskId');
                $stmt->bindParam(':taskId', $taskId);

                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Task deleted successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Task could not be deleted';
                    echo json_encode($response);
                    return;
                }
            }
        }
    }
}


if (isset($decodedParams->scope) && !empty($decodedParams->scope)) {
    if ($decodedParams->scope == 'workspaces') {
        if (isset($decodedParams->action) && !empty($decodedParams->action)) {
            if ($decodedParams->action == 'addWorkspace') {
                $workspaceId = generateRandomString(4);
                $workspaceName = $decodedParams->workspaceName;
                $workspaceCreateDate = $var['timestamp'];
                $workspaceUserId = $decodedParams->workspaceUserId;
                $rightId = generateRandomString(4);

                $stmt = $dbconn->prepare('INSERT INTO workspaces (workspaceId, workspaceUserId, workspaceName, workspaceCreateDate) VALUES (:workspaceId, :workspaceUserId, :workspaceName, :workspaceCreateDate)');
                $stmt->bindParam(':workspaceId', $workspaceId);
                $stmt->bindParam(':workspaceName', $workspaceName);
                $stmt->bindParam(':workspaceCreateDate', $workspaceCreateDate);
                $stmt->bindParam(':workspaceUserId', $workspaceUserId);

                if ($stmt->execute()) {
                    $stmt = $dbconn->prepare('INSERT INTO rights (rightId, rightUserId, rightWorkspaceId) VALUES (:rightId, :userId, :workspaceId)');
                    $stmt->bindParam(':rightId', $rightId);
                    $stmt->bindParam(':userId', $workspaceUserId);
                    $stmt->bindParam(':workspaceId', $workspaceId);
                    if ($stmt->execute()) {
                        $response['status'] = '200';
                        $response['message'] = 'Workspace added successfully!';
                    } else {
                        $response['status'] = '500';
                        $response['message'] = 'Workspace could not be added';
                        echo json_encode($response);
                        return;
                    }
                    $response['status'] = '200';
                    $response['message'] = 'Workspace added successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Workspace could not be added';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'addBoardToWorkspace') {
                $boardId = generateRandomString(4);
                $formData = $decodedParams->formData;
                $boardName = $formData->boardName;
                $boardWorkspaceId = $decodedParams->workspaceId;
                $boardModifydate = $var['timestamp'];
                $boardCreateDate = $var['timestamp'];

                $boardBackgroundId = $formData->boardbackgroundId;

                $maxOrderStmt = $dbconn->prepare('SELECT MAX(boardOrder) as maxOrder FROM boards WHERE boardWorkspaceId = :boardWorkspaceId');
                $maxOrderStmt->bindParam(':boardWorkspaceId', $boardWorkspaceId);
                $maxOrderStmt->execute();
                $maxOrderResult = $maxOrderStmt->fetch(PDO::FETCH_ASSOC);

                $boardOrder = ($maxOrderResult['maxOrder'] !== null) ? $maxOrderResult['maxOrder'] + 1 : 1;

                $stmt = $dbconn->prepare('INSERT INTO boards (boardId, boardName, boardWorkspaceId, boardModifydate, boardBackground, boardCreateDate, boardOrder) VALUES (:boardId, :boardName, :boardWorkspaceId, :boardModifydate, :boardBackground, :boardCreateDate, :boardOrder)');
                $stmt->bindParam(':boardId', $boardId);
                $stmt->bindParam(':boardName', $boardName);
                $stmt->bindParam(':boardWorkspaceId', $boardWorkspaceId);
                $stmt->bindParam(':boardModifydate', $boardModifydate);
                $stmt->bindParam(':boardBackground', $boardBackgroundId);
                $stmt->bindParam(':boardCreateDate', $boardCreateDate);
                $stmt->bindParam(':boardOrder', $boardOrder);

                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Board added successfully!';
                    $response['boardId'] = $boardId;
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Board could not be added';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'drawUsers') {
                $workspaceId = $decodedParams->workspaceId;
                $workspaceOwnerId = $decodedParams->workspaceOwnerId;

                $stmt = $dbconn->prepare('SELECT u.userId, u.userName from rights r INNER JOIN users u ON r.rightUserId = u.userId WHERE r.rightWorkspaceId = :workspaceId ORDER BY (u.userId = :workspaceOwnerId) DESC, u.userId');
                $stmt->bindParam(':workspaceOwnerId', $workspaceOwnerId);
                $stmt->bindParam(':workspaceId', $workspaceId);
                if ($stmt->execute()) {
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['status'] = '200';
                    $response['users'] = $users;
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Users could not be fetched';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'addUserToWorkspace') {
                $userId = $decodedParams->userId;
                $workspaceId = $decodedParams->workspaceId;
                $rightId = generateRandomString(4);

                $stmt = $dbconn->prepare('INSERT INTO rights (rightId, rightUserId, rightWorkspaceId) VALUES (:rightId, :userId, :workspaceId)');
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':workspaceId', $workspaceId);
                $stmt->bindParam(':rightId', $rightId);
                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'User added to workspace successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'User could not be added to workspace';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'removeUserFromWorkspace') {
                $userId = $decodedParams->userId;
                $workspaceId = $decodedParams->workspaceId;

                $stmt = $dbconn->prepare('DELETE FROM rights WHERE rightUserId = :userId AND rightWorkspaceId = :workspaceId');
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':workspaceId', $workspaceId);
                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'User removed from workspace successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'User could not be removed from workspace';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'deleteWorkspace') {
                $workspaceId = $decodedParams->workspaceId;

                $stmt = $dbconn->prepare('SELECT boardId FROM boards WHERE boardWorkspaceId = :workspaceId');
                $stmt->bindParam(':workspaceId', $workspaceId);
                if ($stmt->execute()) {
                    $boards = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($boards as $board) {
                        $boardId = $board['boardId'];
                        $stmt = $dbconn->prepare('UPDATE boards SET boardIsActive = 0 WHERE boardId = :boardId');
                        $stmt->bindParam(':boardId', $boardId);
                        if ($stmt->execute()) {
                            $response['status'] = '200';
                            $response['message'] = 'Workspace deleted successfully!';
                        } else {
                            $response['status'] = '500';
                            $response['message'] = 'Workspace could not be deleted';
                            echo json_encode($response);
                            return;
                        }
                    }
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Boards could not be fetched';
                    echo json_encode($response);
                    return;
                }

                $stmt = $dbconn->prepare('UPDATE workspaces SET workspaceIsActive = 0 WHERE workspaceId = :workspaceId');
                $stmt->bindParam(':workspaceId', $workspaceId);
                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Workspace deleted successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Workspace could not be deleted';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'updateModifyDate') {
                $boardId = $decodedParams->boardId;
                $modifyDate = $var['timestamp'];

                $stmt = $dbconn->prepare('UPDATE boards SET boardModifyDate = :modifyDate WHERE boardId = :boardId');
                $stmt->bindParam(':modifyDate', $modifyDate);
                $stmt->bindParam(':boardId', $boardId);
                if ($stmt->execute()) {
                    $response['status'] = '200';
                    $response['message'] = 'Modify date updated successfully!';
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Modify date could not be updated';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'getBoards') {
                $workspaceId = $decodedParams->workspaceId;

                $stmt = $dbconn->prepare('SELECT boardId, boardName, boardOrder FROM boards WHERE boardWorkspaceId = :workspaceId');
                $stmt->bindParam(':workspaceId', $workspaceId);
                if ($stmt->execute()) {
                    $boards = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['status'] = '200';
                    $response['boards'] = $boards;
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Boards could not be fetched';
                    echo json_encode($response);
                    return;
                }
            }
            if ($decodedParams->action == 'updateBoardOrder') {
                $newBoardOrder = $decodedParams->boards;

                // Assuming you have an 'order' column in your database table
                foreach ($newBoardOrder as $board) {
                    $boardId = $board->boardId;
                    $order = $board->boardOrder;

                    $stmt = $dbconn->prepare('UPDATE boards SET boardOrder = :order WHERE boardId = :boardId');
                    $stmt->bindParam(':order', $order);
                    $stmt->bindParam(':boardId', $boardId);

                    if ($stmt->execute()) {
                        $response['status'] = '200';
                        $response['message'] = 'Board order updated successfully!';
                    } else {
                        $response['status'] = '500';
                        $response['message'] = 'Board order could not be updated';
                        echo json_encode($response);
                        return;
                    }
                }
            }
        }
    }
}

if (isset($decodedParams->scope) && !empty($decodedParams->scope)) {
    if ($decodedParams->scope == 'users') {
        if (isset($decodedParams->action) && !empty($decodedParams->action)) {
            if ($decodedParams->action == 'getUsers') {
                $stmt = $dbconn->prepare('SELECT userId, userName FROM users WHERE userIsActive = 1');
                if ($stmt->execute()) {
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $response['status'] = '200';
                    $response['users'] = $users;
                } else {
                    $response['status'] = '500';
                    $response['message'] = 'Users could not be fetched';
                    echo json_encode($response);
                    return;
                }
            }
        }
    }
}


echo json_encode($response);
return;
