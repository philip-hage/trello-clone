<!DOCTYPE html>
<html>

<head>
  <title>Kanban</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  <script>
    document.getElementsByTagName("html")[0].className += " js";
  </script>
  <?php foreach ($cssStyles as $cssStyle) : ?>
    <link rel="stylesheet" type="text/css" href="<?= $cssStyle ?>" />
  <?php endforeach; ?>
  <?= "<script>var SYSTEM_ADDRESS = '" . $system['address'] . "';</script>" ?>
  <link rel="icon" type="image/x-icon" href="favicon.ico" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>

<body class="overflow-auto custom-scrollbar">

  <div class="toast toast--hidden toast--top-right js-toast toast1" role="alert" aria-live="assertive" aria-atomic="true" id="toast-5">
    <div class="flex items-start justify-between">
      <div class="toast__icon-wrapper toast__icon-wrapper--success margin-right-xs">
        <svg class="icon" viewBox="0 0 16 16">
          <title>Success</title>
          <g>
            <path d="M6,15a1,1,0,0,1-.707-.293l-5-5A1,1,0,1,1,1.707,8.293L5.86,12.445,14.178.431a1,1,0,1,1,1.644,1.138l-9,13A1,1,0,0,1,6.09,15C6.06,15,6.03,15,6,15Z"></path>
          </g>
        </svg>
      </div>

      <div class="text-component text-sm">
        <h1 class="toast__title text-md">Title Five</h1>
        <p class="toast__p">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Explicabo esse maiores assumenda.</p>
      </div>

      <button class="reset toast__close-btn margin-left-xxxxs js-toast__close-btn js-tab-focus">
        <svg class="icon" viewBox="0 0 12 12">
          <title>Close notification</title>
          <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
            <line x1="1" y1="1" x2="11" y2="11" />
            <line x1="11" y1="1" x2="1" y2="11" />
          </g>
        </svg>
      </button>
    </div>
  </div>

  <div class="toast toast--hidden toast--top-right js-toast toast2" role="alert" aria-live="assertive" aria-atomic="true" id="toast-2">
    <div class="flex items-start justify-between">
      <div class="toast__icon-wrapper toast__icon-wrapper--warning margin-right-xs">
        <svg class="icon" viewBox="0 0 16 16">
          <title>Alert</title>
          <path d="M15.8,12.526,9.483.88A1.668,1.668,0,0,0,8.8.2,1.693,1.693,0,0,0,6.516.88L.2,12.526A1.678,1.678,0,0,0,1.686,15H14.314a1.7,1.7,0,0,0,.8-.2,1.673,1.673,0,0,0,.687-2.274ZM8,13a1,1,0,1,1,1-1A1,1,0,0,1,8,13ZM9,9.5a.5.5,0,0,1-.5.5h-1A.5.5,0,0,1,7,9.5v-4A.5.5,0,0,1,7.5,5h1a.5.5,0,0,1,.5.5Z"></path>
          </g>
        </svg>
      </div>

      <div class="text-component text-sm">
        <h1 class="toast__title title2 text-md">Title Five</h1>
        <p class="toast__p p2">Lorem ipsum dolor sit, amet consectetur adipisicing elit. Explicabo esse maiores assumenda.</p>
      </div>

      <button class="reset toast__close-btn margin-left-xxxxs js-toast__close-btn js-tab-focus">
        <svg class="icon" viewBox="0 0 12 12">
          <title>Close notification</title>
          <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
            <line x1="1" y1="1" x2="11" y2="11" />
            <line x1="11" y1="1" x2="1" y2="11" />
          </g>
        </svg>
      </button>
    </div>
  </div>