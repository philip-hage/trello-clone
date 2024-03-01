<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . $system['address'] . 'auth/login');
    exit();
}

session_write_close();
?>

<div id="deleteTaskDialog" class="dialog dialog--sticky js-dialog" data-animation="on">
    <div class="dialog__content max-width-xxs" role="alertdialog" aria-labelledby="dialog-sticky-title" aria-describedby="dialog-sticky-description">
        <h4 id="dialog-sticky-title" class="text-md margin-bottom-xxs">Are you sure you want to permanently delete this section?</h4>
        <p id="dialog-sticky-description" class="text-sm color-contrast-medium">This action cannot be undone.</p>

        <footer class="margin-top-md">
            <div class="flex justify-end gap-xs flex-wrap">
                <button class="btn btn--subtle js-dialog__close">Cancel</button>
                <button onclick="deleteSection()" class="btn btn--accent">Delete</button>
            </div>
        </footer>
    </div>
</div>

<div class="modal modal--animate-scale flex flex-center bg-black bg-opacity-90% padding-md js-modal" id="modal-form">
    <div class="modal__content width-100% max-width-xs max-height-100% overflow-auto padding-md bg radius-md inner-glow shadow-md" role="alertdialog" aria-labelledby="modal-form-title" aria-describedby="modal-form-description">
        <div class="text-component margin-bottom-md">
            <h3 id="modal-form-title">Create Task</h3>
        </div>

        <form class="margin-bottom-sm taskForm">
            <label for="date-input-1" class="form-label margin-bottom-xxs">Task Name</label>
            <div class="flex flex-column flex-row@xs gap-xxxs">
                <input aria-label="taskName" class="form-control flex-grow taskName" name="taskName" type="text" placeholder="Task Name" required>
            </div>
            <br>
            <label for="date-input-1" class="form-label margin-bottom-xxs">Task Description</label>
            <div class="flex flex-column flex-row@xs gap-xxxs">
                <textarea aria-label="taskDescription" class="form-control flex-grow taskDescription" id="taskDescription" name="taskDescription" placeholder="Task Description"></textarea>
            </div>
            <br>
            <div class="cd-form-group">
                <label for="date-input-1" class="cd-label">End Time</label>
                <div class="cd-input-wrapper">
                    <input type="datetime-local" name="endTime" id="date-input-1" class="cd-input">
                </div>
            </div>
            <br>
            <button class="btn btn--primary" onclick="addTaskToSection(event)">Create task</button>
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

<div class="modal modal--animate-scale flex flex-center bg-black bg-opacity-90% padding-md js-modal" id="modal-form-2">
    <div class="modal__content width-100% max-width-xs max-height-100% overflow-auto padding-md bg radius-md inner-glow shadow-md" role="alertdialog" aria-labelledby="modal-form-title" aria-describedby="modal-form-description">
        <div class="text-component margin-bottom-md">
            <h3 class="js-modal-title" id="modal-form-title">Edit Task</h3>
        </div>

        <form class="margin-bottom-sm taskForm js-task-form">
            <label for="date-input-1" class="form-label margin-bottom-xxs">Task Name</label>
            <div class="flex flex-column flex-row@xs gap-xxxs">

                <input aria-label="taskName" class="form-control flex-grow taskName" name="taskName" type="text" placeholder="Task Name">
            </div>
            <br>
            <label for="date-input-1" class="form-label margin-bottom-xxs">Task Description</label>
            <div class="flex flex-column flex-row@xs gap-xxxs">

                <input aria-label="taskDescription" class="form-control flex-grow taskDescription" id="taskDescription" name="taskDescription" type="text" placeholder="Task Description">
            </div>
            <br>
            <label for="date-input-1" class="form-label margin-bottom-xxs">Task Checkboxes</label>
            <div class="flex flex-column flex-row@xs gap-xxxs">
                <li class="checkboxCreate cursor-pointer" onclick="openCheckboxTextarea()"><span class="badge">Create Checkbox</span></li>
            </div>
            <div class="flex flex-column flex-row@xs gap-xxxs">
                <div class="overflow-hidden padding-bottom-md padding-x-md is-hidden js-emoji-rate__comment">
                    <label class="inline-block text-sm color-contrast-medium margin-bottom-xs" for="checkboxDescription">Make your checkbox:</label>

                    <textarea class="form-control width-100% js-checkbox-description" rows="4" name="task-checkbox-name" id="checkboxDescription"></textarea>

                    <div class="margin-top-xs text-right">
                        <button onclick="addCheckboxToTask(event)" class="btn btn--primary">Submit</button>
                    </div>
                    
                </div>
            </div>
            <br>
            <div class="tijdelijkeNaam"></div>
            <div class="flex flex-column flex-row@xs gap-xxxs">
                <fieldset class="margin-bottom-md">
                    <ul class="todo flex flex-column gap-xxxs todoList">
                    </ul>
                </fieldset>

            </div>
            <br>
            <label for="date-input-1" class="form-label margin-bottom-xxs">Task Label</label>
            <?= '<script>var listData = ' . json_encode($taskLabelsByOwner) . '</script>' ?>
            <div class="flex flex-column  flex-row@xs gap-xxxs">
                <div class="autocomplete position-relative  js-autocomplete" data-autocomplete-dropdown-visible-class="autocomplete--results-visible">

                    <div class="position-relative">
                        <input class="form-control width-100% js-autocomplete__input autocompleteInput" type="text" name="taskLabelInput" id="autocomplete-input" placeholder="Task Label" autocomplete="off">

                        <div class="autocomplete__loader position-absolute top-0 right-0 padding-right-sm height-100% flex items-center" aria-hidden="true">
                            <div class="circle-loader circle-loader--v1">
                                <div class="circle-loader__circle"></div>
                            </div>
                        </div>
                    </div>

                    <!-- dropdown -->
                    <div class="autocomplete__results  js-autocomplete__results">
                        <ul id="autocomplete1" class="autocomplete__list js-autocomplete__list">
                            <li class="autocomplete__item padding-y-xs padding-x-sm text-truncate js-autocomplete__item is-hidden"></li>
                        </ul>
                    </div>

                    <p class="sr-only" aria-live="polite" aria-atomic="true"><span class="js-autocomplete__aria-results">0</span> results found.</p>
                </div>
            </div>
            <br>
            <div class="cd-form-group">
                <label for="date-input-1" class="cd-label">End Time</label>
                <div class="cd-input-wrapper">
                    <input type="datetime-local" name="taskEndTime" id="date-input-1" class="cd-input">
                </div>
            </div>
            <br>
            <button class="btn btn--primary" onclick="editTask(event)">Edit task</button>
            <button class="btn btn--accent" onclick="deleteTask()">Delete task</button>
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

<div class="board" style="background-image: url(&quot;<?= $board['boardbackgroundUrl'] ?>&quot;);">
    <header class="header position-relative js-header navbarHeader">
        <div class="header__container container max-width-lg">
            <div class="header__logo">
                <a class="logo-header" href="<?= $system['address'] ?>workspaces/overview/<?= $_SESSION['user']['userId'] ?>">
                    <h1 class="header-text">Frello</h1>
                </a>
            </div>
            <h3 class="board-text"><?= $board['boardName'] ?></h3>


            <button class="btn btn--subtle header__trigger js-header__trigger" aria-label="Toggle menu" aria-expanded="false" aria-controls="header-nav">
                <i class="header__trigger-icon" aria-hidden="true"></i>
                <span>Menu</span>
            </button>

            <nav class="header__nav js-header__nav" id="header-nav" role="navigation" aria-label="Main">
                <div class="header__nav-inner">
                    <div class="header__label">Main menu</div>
                    <ul class="header__list">
                        <a href="<?= $system['address'] ?>workspaces/overview/<?= $_SESSION['user']['userId'] ?>" class="btn btn--primary headerBtn">Workspaces</a>
                        <?php if ($_SESSION['user']) : ?>
                            <a href="<?= $system['address'] ?>auth/logout" class="btn btn--primary headerBtn">Logout</a>
                        <?php else : ?>
                            <a href="<?= $system['address'] ?>auth/register" class="btn btn--primary headerBtn">Sign Up</a>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <form id="section-form">
        <div class="col-2 margin-right-sm">
            <input class="sectionInput" type="text" placeholder="New Section..." id="section-input" required />
        </div>
        <div class="select margin-right-sm col-2">
            <select class="select__input btn btn--subtle sectionColor" name="select-this" id="select-this" required>
                <option value="red">Red</option>
                <option value="blue">Blue</option>
                <option value="green">Green</option>
                <option value="yellow">Yellow</option>
                <option value="purple">Purple</option>
                <option value="pink">Pink</option>
            </select>

            <svg class="icon select__icon" aria-hidden="true" viewBox="0 0 16 16">
                <polyline points="1 5 8 12 15 5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
        </div>
        <br>
        <button class="col-2" onclick="addSection(event)">Add +</button>
    </form>

    <div class="lanes">
        <?php foreach ($sections as $section) : ?>
            <div class="swim-lane js-sectionid-<?= $section['sectionId'] ?> color-<?= $section['sectionColor'] ?>">
                <div class="justify-between">
                    <h3 class="heading"><?= $section['sectionName']; ?></h3>
                    <div>
                        <i class="fa-solid fa-trash default-icon pointer" onclick="setSectionId('<?= $section['sectionId'] ?>')" aria-controls="deleteTaskDialog"></i>
                        <i class="fa-solid fa-plus default-icon pointer" onclick="setSectionId('<?= $section['sectionId'] ?>')" aria-controls="modal-form"></i>
                    </div>
                </div>
                <?php foreach ($tasks as $task) : ?>
                    <?php if ($task['taskSectionId'] == $section['sectionId']) : ?>
                        <div aria-controls="modal-form-2" onclick="drawTaskUpdate('<?= $task['taskId'] ?>')" class="task js-taskid-<?= $task['taskId'] ?>" draggable="true" data-task-id="<?= $task['taskId'] ?>" data-task-order="<?= $task['taskOrder'] ?>">
                            <h4 class="taskName"><?= $task['taskName'] ?></h4>
                            <div class="taskLabel-slider overflow-auto custom-scrollbar">
                                <?php foreach ($taskLabels as $taskLabel) : ?>
                                    <?php if ($taskLabel['taskId'] == $task['taskId']) : ?>
                                        <div class="badge badge--primary-light text-sm taskLabel js-labelId-<?= $taskLabel['labelId'] ?>" onclick="removeBadge('<?= $taskLabel['labelId'] ?>')">
                                            <?= $taskLabel['labelName'] ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <p class="card-v10__label text-uppercase <?= ($task['taskEndTime'] !== null && $task['taskEndTime'] < time()) ? 'color-accent' : 'color-primary' ?> letter-spacing-md">
                                <?= ($task['taskEndTime'] !== null) ? date('d-m-Y H:i', $task['taskEndTime']) : '' ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>