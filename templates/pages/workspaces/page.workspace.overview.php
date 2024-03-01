<?php
session_start(); // Start the session
// Check if user is not authenticated
if (!isset($_SESSION['user'])) {
    header('Location: ' . $system['address'] . 'auth/login'); // Redirect to the login page
    exit(); // Stop further execution
}

session_write_close();
?>

<header class="header position-relative js-header ">
    <div class="header__container container max-width-lg">
        <div class="header__logo">
            <a href="<?= $system['address'] ?>workspaces/overview/<?= $_SESSION['user']['userId'] ?>" class="logo-header">
                <h1>Frello</h1>
            </a>
        </div>

        <button class="btn btn--subtle header__trigger js-header__trigger" aria-label="Toggle menu" aria-expanded="false" aria-controls="header-nav">
            <i class="header__trigger-icon" aria-hidden="true"></i>
            <span>Menu</span>
        </button>

        <nav class="header__nav js-header__nav" id="header-nav" role="navigation" aria-label="Main">
            <div class="header__nav-inner">
                <div class="header__label">Main menu</div>
                <ul class="header__list">
                    <button aria-controls="modal-form" class="btn btn--primary margin-right-md">Create Workspace</button>
                    <?php if ($_SESSION['user']) : ?>
                        <a href="<?= $system['address'] ?>auth/logout" class="btn btn--primary">Logout</a>
                    <?php else : ?>
                        <a href="<?= $system['address'] ?>auth/register" class="btn btn--primary">Sign Up</a>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</header>

<div id="deleteWorkspaceDialog" class="dialog dialog--sticky js-dialog" data-animation="on">
    <div class="dialog__content max-width-xxs" role="alertdialog" aria-labelledby="dialog-sticky-title" aria-describedby="dialog-sticky-description">
        <h4 id="dialog-sticky-title" class="text-md margin-bottom-xxs">Are you sure you want to permanently delete this workspace?</h4>
        <p id="dialog-sticky-description" class="text-sm color-contrast-medium">This action cannot be undone.</p>

        <footer class="margin-top-md">
            <div class="flex justify-end gap-xs flex-wrap">
                <button class="btn btn--subtle js-dialog__close">Cancel</button>
                <?php $userId = $_SESSION['user']['userId']; ?>
                <button onclick="deleteWorkspace('<?= $userId ?>')" class="btn btn--accent">Delete</button>
            </div>
        </footer>
    </div>
</div>

<div class="modal modal--animate-scale flex flex-center bg-black bg-opacity-90% padding-md js-modal" id="modal-form">
    <div class="modal__content width-100% max-width-xs max-height-100% overflow-auto padding-md bg radius-md inner-glow shadow-md" role="alertdialog" aria-labelledby="modal-form-title" aria-describedby="modal-form-description">
        <div class="text-component margin-bottom-md">
            <h3 id="modal-form-title">Create Workspace</h3>
        </div>

        <form class="margin-bottom-sm taskForm">
            <div class="flex flex-column flex-row@xs gap-xxxs">
                <input aria-label="workspaceName" class="form-control flex-grow workspaceName" name="workspaceName" type="text" placeholder="Workspace Name">
            </div>
            <br>
            <button class="btn btn--primary" onclick="addWorkspace(event)">Create workspace</button>
        </form>
    </div>

    <button class="reset modal__close-btn modal__close-btn--outer  js-modal__close js-tab-focus">
        <svg class="icon icon--sm" viewBox="0 0 24 24">
            <title>Close modal window</title>
            <g fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="3" x2="21" y2="21" />
                <line x1="21" y1="3" x2="3" y2="21" />
            </g>
        </svg>
    </button>
</div>

<div class="modal modal--animate-scale flex flex-center bg-black bg-opacity-90% padding-md js-modal" id="modal-form-workspace">
    <div class="modal__content width-100% max-width-xs max-height-100% overflow-auto padding-md bg radius-md inner-glow shadow-md" role="alertdialog" aria-labelledby="modal-form-title" aria-describedby="modal-form-description">
        <div class="text-component margin-bottom-md">
            <h3 id="modal-form-title">Create Board</h3>
        </div>

        <form class="margin-bottom-sm js-board-form">
            <div class="flex flex-column flex-row@xs gap-xxxs">
                <input aria-label="boardName" class="form-control flex-grow boardName" name="boardName" type="text" placeholder="Board Name" required>
            </div>
            <br>
            <div class="flex flex-column flex-row@xs gap-xxxs">
                <fieldset>
                    <legend class="form-legend margin-bottom-xs">Select option:</legend>

                    <div class="grid gap-sm js-choice-imgs" role="radiogroup">
                        <?php foreach ($boardBackgrounds as $boardBackground) : ?>
                            <div class="choice-img bg-light radius-md col-6@xs col-4@sm col-3@md js-choice-img js-tab-focus width-xxxxl" role="radio" aria-checked="false" tabindex="0" aria-label="Option One" data-boardbackground-id="<?= $boardBackground['boardbackgroundId'] ?>">

                                <figure class="aspect-ratio-4:3">
                                    <img class="block width-100%" src="<?= $boardBackground['boardbackgroundUrl'] ?>" alt="Image description">
                                </figure>

                                <div class="choice-img__input" aria-hidden="true">
                                    <svg class="icon" viewBox="0 0 16 16">
                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2 8l4 4 8-8" />
                                    </svg>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </fieldset>
            </div>
            <br>
            <button class="btn btn--primary" onclick="addBoardToWorkspace(event)">Create Board</button>
        </form>
    </div>

    <button class="reset modal__close-btn modal__close-btn--outer  js-modal__close js-tab-focus">
        <svg class="icon icon--sm" viewBox="0 0 24 24">
            <title>Close modal window</title>
            <g fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="3" x2="21" y2="21" />
                <line x1="21" y1="3" x2="3" y2="21" />
            </g>
        </svg>
    </button>
</div>

<div class="modal modal--animate-scale flex flex-center bg-black bg-opacity-90% padding-md js-modal" id="modal-form-users">
    <div class="modal__content width-100% max-width-xs max-height-100% overflow-auto padding-md bg radius-md inner-glow shadow-md" role="alertdialog" aria-labelledby="modal-form-title" aria-describedby="modal-form-description">
        <div class="text-component margin-bottom-md">
            <h3 id="modal-form-title">Add users</h3>
        </div>

        <form class="margin-bottom-sm addUser">
            <p class="name-line">
                <span class="name-user">
                    Name
                </span>
            </p>
            <div class="members">
                <!-- Container to hold user details -->
                <div class="user-details-container"></div>
            </div>
            <br>
            <label style="display: none;" class="form-label margin-bottom-xxxs select-user" for="select-this">Select user:</label>
            <div class="select js-select-user" style="display: none;">
                <select class="select__input btn btn--subtle js-add-user-select" name="select-this" id="select-this">

                </select>

                <svg class="icon select__icon" aria-hidden="true" viewBox="0 0 16 16">
                    <polyline points="1 5 8 12 15 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                </svg>
            </div>
            <div class="flex flex-column flex-row@xs gap-xxxs">
            </div>
            <br>
            <button style="display: none;" class="btn btn--primary js-user-add" onclick="addUserToWorkspace(event)">Add User</button>
        </form>
    </div>

    <button class="reset modal__close-btn modal__close-btn--outer  js-modal__close js-tab-focus">
        <svg class="icon icon--sm" viewBox="0 0 24 24">
            <title>Close modal window</title>
            <g fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="3" x2="21" y2="21" />
                <line x1="21" y1="3" x2="3" y2="21" />
            </g>
        </svg>
    </button>
</div>
<?php if (!empty($recentlyBoards)) : ?>
    <div class="watched-boards">
        <h3 class="text-center"><i class="fa-regular fa-clock"></i> Recently Modified</h3>
        <br>
        <div class="container max-width-adaptive-lg">
            <div class="workspace-container card-v14 justify-between">
                <div class="flex@md boards-container custom-scrollbar">
                    <?php foreach ($recentlyBoards as $recentlyBoard) : ?>
                        <div class="col-4@xs padding-sm">
                            <div class="card-v2 radius-md">
                                <a onclick="setWorkspaceAndBoardId('<?= $recentlyBoard['boardWorkspaceId'] ?>', '<?= $recentlyBoard['boardId'] ?>')" class="<?= $recentlyBoard['boardId'] ?>" href="<?= $system['address'] ?>boards/overview/<?= $recentlyBoard['boardId'] ?>">
                                    <figure class="card-image">
                                        <img src="<?= $recentlyBoard['boardbackgroundUrl'] ?>" alt="">
                                    </figure>
                                </a>
                                <figcaption class="card-v2__caption padding-x-sm padding-top-md padding-bottom-sm text-center">
                                    <div class="text-md text-base@md"><?= $recentlyBoard['boardName'] ?></div>
                                </figcaption>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <?php if (!empty($workspaces)) : ?>
        <div class="workspace">
            <h3 class="text-center">Your workspaces</h3>
            <br>
            <div class="container max-width-adaptive-lg">
                <?php foreach ($workspaces as $workspace) : ?>
                    <div class="workspace-container card-v14 justify-between">
                        <div class="workspace-headers">
                            <h4><?= $workspace['workspaceName'] ?></h4>
                            <div class="workspace-btns">
                                <button aria-controls="modal-form-users" onclick="drawUserAdd('<?= $workspace['workspaceId'] ?>', '<?= $workspace['workspaceUserId'] ?>', '<?= $_SESSION['user']['userId'] ?>')" class="btn btn--primary">Users</button>
                                <?php if ($workspace['workspaceUserId'] == $_SESSION['user']['userId']) : ?>
                                    <button aria-controls="deleteWorkspaceDialog" onclick="setWorkspaceId('<?= $workspace['workspaceId'] ?>')" class="btn btn--primary">Delete Workspace</button>
                                <?php endif; ?>
                            </div>
                        </div>



                        <div class="flex@md boards-container custom-scrollbar">
                            <?php
                            $workspaceId = $workspace['workspaceId'];
                            $boards = $dbconn->prepare('SELECT b.boardId, b.boardName, bg.boardbackgroundUrl FROM boards b INNER JOIN boardbackgrounds bg ON b.boardBackground = bg.boardbackgroundId WHERE boardWorkspaceId = :workspaceId ORDER BY boardOrder ASC');
                            $boards->bindParam(':workspaceId', $workspaceId);
                            $boards->execute();
                            $boards = $boards->fetchAll(PDO::FETCH_ASSOC);
                            ?>

                            <?php foreach ($boards as $board) : ?>
                                <div class="col-4@xs padding-sm">
                                    <div class="card-v2 radius-md boardCards">
                                        <a onclick="setWorkspaceAndBoardId('<?= $workspaceId  ?>', '<?= $board['boardId'] ?>')" class="<?= $workspaceId ?>" href="<?= $system['address'] ?>boards/overview/<?= $board['boardId'] ?>">
                                            <figure class="card-image cursor-pointer">
                                                <?php if (!empty($board['boardbackgroundUrl'])) : ?>
                                                    <img src="<?= $board['boardbackgroundUrl'] ?>" alt="">
                                                <?php else : ?>
                                                    <img src="<?= $system['address'] ?>assets/img/cHJpdmF0ZS9sci9pbWFnZXMvd2Vic2l0ZS8yMDIyLTA4L2pvYjEwMzQtZWxlbWVudC0wNi0zOTcucG5n.png" alt="">
                                                <?php endif; ?>
                                            </figure>
                                            <figcaption class="card-v2__caption padding-x-sm padding-top-md padding-bottom-sm text-center">
                                                <div class="text-md text-base@md"><?= $board['boardName'] ?></div>
                                            </figcaption>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Add "Create Board" button -->
                            <div class="col-4@xs padding-sm">
                                <div class="card-v2 createBoard radius-md">
                                    <figure aria-controls="modal-form-workspace" onclick="setWorkspaceId('<?= $workspaceId ?>')" class="card-image cursor-pointer <?= $workspaceId ?>">
                                        <img src="<?= $system['address'] ?>assets/img/white.png" alt="">
                                    </figure>
                                    <figcaption class="card-v2__caption padding-x-sm padding-top-md padding-bottom-sm text-center">
                                        <div class="text-md text-base@md">Create Board</div>
                                    </figcaption>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="container max-width-adaptive-lg text-container">
            <div class="empty-state-v1 flex">
                <div class="empty-state-v1__content">
                    <h3 class="empty-state-v1__title">No workspaces found</h3>
                    <br>
                    <a aria-controls="modal-form" class="btn btn--primary">Create Workspace to get started</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>