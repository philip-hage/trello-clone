const draggables = document.querySelectorAll(".task");
const droppables = document.querySelectorAll(".swim-lane");

draggables.forEach((task) => {
  task.addEventListener("dragstart", () => {
    task.classList.add("is-dragging");
  });
  task.addEventListener("dragend", () => {
    task.classList.remove("is-dragging");
  });
});

droppables.forEach((zone) => {
  zone.addEventListener("dragover", (e) => {
    e.preventDefault();
    const bottomTask = insertAboveTask(zone, e.clientY);
    const curTask = document.querySelector(".is-dragging");

    if (!bottomTask) {
      zone.appendChild(curTask);
    } else {
      zone.insertBefore(curTask, bottomTask);
    }
  });
  zone.addEventListener("drop", (e) => {
    e.preventDefault();
    const bottomTask = insertAboveTask(zone, e.clientY);
    const curTask = document.querySelector(".is-dragging");

    if (!bottomTask) {
      zone.appendChild(curTask);
    } else {
      zone.insertBefore(curTask, bottomTask);
    }

    // Update task orders after drop
    updateTaskOrders(zone);
  });
});

const insertAboveTask = (zone, mouseY) => {
  const els = zone.querySelectorAll(".task:not(.is-dragging)");

  let closestTask = null;
  let closestOffset = Number.NEGATIVE_INFINITY;

  els.forEach((task) => {
    const { top } = task.getBoundingClientRect();

    const offset = mouseY - top;

    if (offset < 0 && offset > closestOffset) {
      closestOffset = offset;
      closestTask = task;
    }
  });

  return closestTask;
};

function updateTaskOrders(zone) {
  const tasks = zone.querySelectorAll(".task");
  tasks.forEach((task, index) => {
    const taskId = task.dataset.taskId;
    const sectionId = zone.classList[1].split("-").pop();
    updateTaskOrder(taskId, sectionId, index + 1);
  });
}

async function updateTaskOrder(taskId, sectionId, taskOrder) {
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "updateTaskOrder",
      taskId: taskId,
      sectionId: sectionId,
      taskOrder: taskOrder,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    updateModifyDate();
  }
}

async function updateTaskSection(taskId, sectionId) {
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "updateTaskSection",
      taskId: taskId,
      sectionId: sectionId,
    }),
  });
}

function setSectionId(sectionId) {
  localStorage.setItem("sectionId", sectionId);
}

async function addTaskToSection(event) {
  event.preventDefault();
  const sectionId = localStorage.getItem("sectionId");
  const taskName = document.querySelector(".taskName").value;
  const taskDescription = document.querySelector(".taskDescription").value;
  const EndTime = document.querySelector(".cd-input").value;

  if (taskName === "") {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Please fill in the task name");
    localStorage.setItem("toasttitle", "Failed");
    location.reload();
    return;
  }

  if (EndTime !== "") {
    // Parse the date and time components from the ISO string
    const [datePart, timePart] = EndTime.split("T");
    const [year, month, day] = datePart.split("-").map(Number);

    // Create a new Date object with the parsed components
    const date = new Date(year, month - 1, day); // Note: month is 0-indexed in JavaScript Date

    // Parse the time components
    const [hours, minutes] = timePart.split(":").map(Number);

    // Set the hours and minutes in the date object
    date.setHours(hours);
    date.setMinutes(minutes);

    if (!isNaN(date.getTime())) {
      // Valid date, proceed with using date.getTime() / 1000
      var taskEndTime = date.getTime() / 1000;
    } else {
      console.log("Invalid date");
    }
  } else {
    var taskEndTime = null;
  }

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "addTask",
      sectionId: sectionId,
      taskName: taskName,
      taskOrder: 1,
      taskDescription: taskDescription,
      taskEndTime: taskEndTime,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    updateModifyDate();
    location.reload();
  }
}

async function addSection(event) {
  event.preventDefault();
  const sectionName = document.querySelector(".sectionInput").value;
  const sectionColor = document.querySelector(".sectionColor").value;

  if (sectionName === "" || sectionColor === "") {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Please fill in the section name");
    localStorage.setItem("toasttitle", "Failed");
    location.reload();
    return;
  }

  // Extract sectionBoardId from the URL
  const urlParts = window.location.pathname.split("/");
  const sectionBoardId = urlParts[urlParts.indexOf("overview") + 1];

  // Now you can use sectionBoardId in your fetch request
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "addSection",
      sectionName: sectionName,
      sectionBoardId: sectionBoardId,
      sectionColor: sectionColor,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    updateModifyDate();
    location.reload();
  } else {
    localStorage.setItem("toast", "false");
    localStorage.setItem(
      "toastMessage",
      "Adding section failed please contact the support team"
    );
    localStorage.setItem("toasttitle", "Failed");
  }
}

async function updateModifyDate() {
  const boardId = localStorage.getItem("boardId");
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "workspaces",
      action: "updateModifyDate",
      boardId: boardId,
    }),
  });
}

async function reorderBoards(boardId, workspaceId) {
  // Fetch the existing boards from the server
  const boardsResponse = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "workspaces",
      action: "getBoards",
      workspaceId: workspaceId,
    }),
  });

  const boardsData = await boardsResponse.json();

  if (boardsData.status === "200") {
    const boards = boardsData.boards;

    // Update the order based on the clicked board
    boards.forEach((board) => {
      if (board.boardId === boardId) {
        board.boardOrder = 1;
      } else {
        board.boardOrder = board.boardOrder + 1;
      }
    });

    // Sort boards based on the new order
    boards.sort((a, b) => a.boardOrder - b.boardOrder);

    // Update the server with the new order
    const updateOrderCall = await fetch(SYSTEM_ADDRESS + "ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        scope: "workspaces",
        action: "updateBoardOrder",
        boards: boards,
      }),
    });

    const updateOrderData = await updateOrderCall.json();

    if (updateOrderData.status === "200") {
      console.log("Board order updated successfully!");
    } else {
      // Handle error if needed
      console.error(updateOrderData.message);
    }
  } else {
    // Handle error if needed
    console.error(boardsData.message);
  }
}

async function deleteSection() {
  const sectionId = localStorage.getItem("sectionId");
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "deleteSection",
      sectionId: sectionId,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    updateModifyDate();
    location.reload();
  } else {
    localStorage.setItem("toast", "false");
    localStorage.setItem(
      "toastMessage",
      "deleting board failed please contact the support team"
    );
    localStorage.setItem("toasttitle", "Failed");
  }
}

async function drawTaskUpdate(taskId) {
  localStorage.setItem("taskId", taskId);
  const modal = document.querySelector("#modal-form-2");
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "getTask",
      taskId: taskId,
    }),
  });

  const response = await call.json();
  if (response.status === "200") {
    const task = response.task;
    modal.querySelector(".js-modal-title").textContent = task["taskName"];
    modal.querySelector(".taskName").value = task["taskName"];
    modal.querySelector(".taskDescription").value = task.taskDescription;

    const checkBoxList = await fetch(SYSTEM_ADDRESS + "ajax.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        scope: "boards",
        action: "getCheckboxList",
        taskId: taskId,
      }),
    });

    const checkBoxListResponse = await checkBoxList.json();
    if (checkBoxListResponse.status === "200") {
      const checkBoxes = checkBoxListResponse.checklist;

      const tijdelijkeNaam = document.querySelector(".tijdelijkeNaam");
      tijdelijkeNaam.innerHTML = "";

      const ul = document.querySelector(".todoList");
      ul.innerHTML = "";

      checkBoxes.forEach((checkbox) => {
        const todoLi = document.createElement("li");
        todoLi.onclick = function (event) {
          const checkboxInput =
            event.target.parentElement.querySelector(".todo__input");

          if (checkboxInput && checkboxInput == event.target) {
            if (checkboxInput.checked) {
              const hiddenInput = document.createElement("input");
              hiddenInput.type = "hidden";
              hiddenInput.name = "checkboxListIds[]";
              hiddenInput.value = checkboxInput.value;
              tijdelijkeNaam.appendChild(hiddenInput);
            } else {
              const hiddenInput = tijdelijkeNaam.querySelector(
                `input[value="${checkboxInput.value}"]`
              );
              hiddenInput.remove();
            }
          }
        };

        const todoLabel = document.createElement("label");
        todoLabel.classList.add("todo__item");

        const todoInput = document.createElement("input");
        todoInput.classList.add(
          "todo__input",
          "checkbox",
          "js-checkbox-list-id-" + checkbox.checklistId
        );
        todoInput.type = "checkbox";
        todoInput.id = "checkbox-1";
        todoInput.value = checkbox.checklistId;
        todoInput.onclick = function () {};

        const todoSpan = document.createElement("span");
        todoSpan.classList.add("todo__checkbox");
        todoSpan.ariaHidden = "true";

        var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.setAttribute("class", "icon");
        svg.setAttribute("viewBox", "0 0 16 16");

        // Create path element
        var path = document.createElementNS(
          "http://www.w3.org/2000/svg",
          "path"
        );
        path.setAttribute("fill", "none");
        path.setAttribute("stroke", "currentColor");
        path.setAttribute("stroke-linecap", "round");
        path.setAttribute("stroke-linejoin", "round");
        path.setAttribute("stroke-width", "2");
        path.setAttribute("d", "M2 8l4 4 8-8");

        // Append path to SVG
        svg.appendChild(path);

        // Append SVG to todoSpan
        todoSpan.appendChild(svg);

        const todoSpanTodoLabel = document.createElement("span");
        todoSpanTodoLabel.classList.add("todo__label");
        todoSpanTodoLabel.textContent = checkbox.checklistDescription;

        // Append everything to the corresponding parent elements
        todoLabel.appendChild(todoInput);
        todoLabel.appendChild(todoSpan);
        todoLabel.appendChild(todoSpanTodoLabel);

        todoLi.appendChild(todoLabel);

        ul.appendChild(todoLi);

        if (checkbox.checklistIsChecked == 1) {
          todoInput.checked = true;

          const hiddenInput = document.createElement("input");
          hiddenInput.type = "hidden";
          hiddenInput.name = "checkboxListIds[]";
          hiddenInput.value = checkbox.checklistId;
          tijdelijkeNaam.appendChild(hiddenInput);
        }
      });
    }

    if (task.EndTime != "") {
      const timestamp = task.taskEndTime * 1000; // Convert to milliseconds
      const date = new Date(timestamp);

      var tzoffset = new Date().getTimezoneOffset() * 60000; //offset in milliseconds
      var formattedDate = new Date(date - tzoffset).toISOString().slice(0, 16);

      modal.querySelector(".cd-input").value = formattedDate;
    } else {
      modal.querySelector(".cd-input").value = "";
    }
  }
}

async function addCheckboxToTask(event) {
  event.preventDefault();
  const taskId = localStorage.getItem("taskId");
  const checkboxDescription = document.querySelector(
    ".js-checkbox-description"
  ).value;

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "addCheckbox",
      taskId: taskId,
      checkboxDescription: checkboxDescription,
    }),
  });

  const response = await call.json();
  if (response.status === "200") {
    location.reload();
  }
}

async function editTask(event) {
  event.preventDefault();
  const taskId = localStorage.getItem("taskId");

  // Assuming you have an HTML form with the class 'js-form'
  var rawFormData = new FormData(document.querySelector(".js-task-form"));
  // Update an object to store form data
  var formData = {};
  // Iterate over FormData using for...of loop
  for (var pair of rawFormData.entries()) {
    var key = pair[0];
    var value = pair[1];

    // Check if the key ends with square brackets
    if (key.endsWith("[]")) {
      // If it ends with square brackets, treat it as an array
      key = key.slice(0, -2); // Remove the brackets from the key
      if (!formData.hasOwnProperty(key)) {
        formData[key] = [];
      }
      formData[key].push(value);
    } else {
      // If it doesn't end with square brackets, treat it as a single value
      formData[key] = value;
    }
  }

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "updateTask",
      taskId: taskId,
      formData: formData,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    updateModifyDate();
    location.reload();
  }
}

// your input field
var autocomplete = document.getElementsByClassName("autocomplete")[0];

// Replace static array with dynamic data
var searchValues = listData.map(function (item) {
  return { label: item.labelName };
});

// initialize the Autocomplete object
new Autocomplete({
  element: autocomplete,
  searchData: function (query, cb, eventType) {
    var data = searchValues.filter(function (item) {
      // return item if item['label'] contains 'query'
      return item.label.toLowerCase().indexOf(query.toLowerCase()) > -1;
    });
    // make sure to call the callback function and pass the data array as its argument
    cb(data);
    // eventType can be 'focus' or 'type'
  },
});

async function removeBadge(labelId) {
  const taskId = localStorage.getItem("taskId");
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "removeLabel",
      labelId: labelId,
      taskId: taskId,
    }),
  });

  const response = await call.json();

  if (response.status === "200") {
    const boardId = localStorage.getItem("boardId");

    var pathAfterOverview = window.location.pathname.substring(
      "/boards/overview/".length
    );

    window.location.href = `${SYSTEM_ADDRESS}boards/overview/${pathAfterOverview}`;
  }
}

// Your existing function
function openCheckboxTextarea() {
  const textArea = document.querySelector(".js-emoji-rate__comment");
  // check if the textarea has the class is-hidden if so remove it
  if (textArea.classList.contains("is-hidden")) {
    textArea.classList.remove("is-hidden");
  } else {
    textArea.classList.add("is-hidden");
  }
}

async function deleteTask() {
  const taskId = localStorage.getItem("taskId");
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "boards",
      action: "deleteTask",
      taskId: taskId,
    }),
  });
  const response = await call.json();
}
