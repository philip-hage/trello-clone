async function addWorkspace(event) {
  event.preventDefault();
  const workspaceName = document.querySelector(".workspaceName").value;

  if (workspaceName === "") {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Please fill in the workspace name");
    localStorage.setItem("toasttitle", "Failed");
    location.reload();
    return;
  }

  const urlParts = window.location.pathname.split("/");
  const workspaceUserId = urlParts[urlParts.indexOf("overview") + 1];

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "workspaces",
      action: "addWorkspace",
      workspaceName: workspaceName,
      workspaceUserId: workspaceUserId,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    location.reload();
  }
}

function setWorkspaceAndBoardId(workspaceId, boardId) {
  localStorage.setItem("workspaceId", workspaceId);
  localStorage.setItem("boardId", boardId);
}

function setWorkspaceId(workspaceId) {
  localStorage.setItem("workspaceId", workspaceId);
}

async function addBoardToWorkspace(event) {
  event.preventDefault();

  const boardName = document.querySelector(".boardName").value;

  if (boardName === "") {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Please choose a name for the board");
    localStorage.setItem("toasttitle", "Failed");
    location.reload();
    return;
  }

  var selectedChoice = document.querySelector(
    ".js-choice-imgs [aria-checked=true]"
  );

  if (selectedChoice === null) {
    localStorage.setItem("toast", "false");
    localStorage.setItem(
      "toastMessage",
      "Please choose a background for the board"
    );
    localStorage.setItem("toasttitle", "Failed");
    location.reload();
    return;
  }

  var selectedBoardBackgroundId = selectedChoice.getAttribute(
    "data-boardbackground-id"
  );

  // Assuming you have an HTML form with the class 'js-form'
  var rawFormData = new FormData(document.querySelector(".js-board-form"));
  rawFormData.append("boardbackgroundId", selectedBoardBackgroundId);
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

  const workspaceId = localStorage.getItem("workspaceId");

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "workspaces",
      action: "addBoardToWorkspace",
      formData: formData,
      workspaceId: workspaceId,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    const boardId = response.boardId;
    window.location.href = `${SYSTEM_ADDRESS}boards/overview/${boardId}`;
  }
}

async function drawUserAdd(workspaceId, workspaceOwnerId, userId) {
  localStorage.setItem("workspaceId", workspaceId);

  const isUserOwner = workspaceOwnerId === userId;

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "workspaces",
      action: "drawUsers",
      workspaceId: workspaceId,
      workspaceOwnerId: workspaceOwnerId,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    // Select the container for user details
    const userDetailsContainer = document.querySelector(
      ".user-details-container"
    );

    // Clear existing content
    userDetailsContainer.innerHTML = "";

    // Iterate through the users and create elements for each user
    if (isUserOwner) {
      const label = document.querySelector(".select-user");
      label.style.display = "block";
      const addButton = document.querySelector(".js-user-add");
      addButton.style.display = "block";
      const getUsers = await fetch(SYSTEM_ADDRESS + "ajax.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          scope: "users",
          action: "getUsers",
        }),
      });

      const usersResponse = await getUsers.json();
      if (usersResponse.status === "200") {
        const form = document.querySelector(".addUser");
        const selectInput = document.querySelector(".js-add-user-select");

        const formLabel = form.querySelector(".form-label");
        formLabel.style.display = "block";

        const userSelect = document.querySelector('.js-select-user');
        userSelect.style.display = "block";

        const workspaceUsers = response.users.map((user) => user.userId);

        // Filter out users that are already in the workspace
        const filteredUsers = usersResponse.users.filter(
          (user) => !workspaceUsers.includes(user.userId)
        );

        // Clear existing content of divFlexColumn
        selectInput.innerHTML = "";

        if (filteredUsers.length === 0) {
          const option = document.createElement("option");
          option.setAttribute("value", "");
          option.innerHTML = "No users available";
          selectInput.appendChild(option);
        }

        // Loop through filtered users and create option elements
        filteredUsers.forEach((user) => {
          const option = document.createElement("option");
          option.setAttribute("value", user.userId);
          option.innerHTML = user.userName;
          selectInput.appendChild(option);
        });
      }

      response.users.forEach((user) => {
        const userListDetail = document.createElement("div");
        userListDetail.classList.add("member-list-item-detail");

        const userDetails = document.createElement("div");
        userDetails.classList.add("details");

        const userOptions = document.createElement("div");
        userOptions.classList.add("options");

        const userName = document.createElement("p");
        userName.classList.add("user-username");

        const spanUserName = document.createElement("span");
        spanUserName.classList.add(
          "user-username",
          "span-username",
          "u-inline-block"
        );
        spanUserName.innerHTML = user.userName;

        if (user.userId === userId) {
          const removeSpan = document.createElement("span");
          removeSpan.innerHTML = "Owner";

          userOptions.appendChild(removeSpan);
        } else {
          const removeButton = document.createElement("a");
          removeButton.classList.add("remove-button", "cursor-pointer");
          removeButton.setAttribute(
            "onclick",
            `removeUserFromWorkspace('${user.userId}')`
          );

          const removeSpan = document.createElement("span");
          removeSpan.innerHTML = "Remove";

          userOptions.appendChild(removeButton);
          removeButton.appendChild(removeSpan);
        }

        userListDetail.appendChild(userDetails);
        userListDetail.appendChild(userOptions);

        userDetails.appendChild(userName);
        userName.appendChild(spanUserName);

        userDetailsContainer.appendChild(userListDetail);
      });
    } else {
      const label = document.querySelector(".select-user");
      label.style.display = "none";
      const userSelect = document.querySelector('.js-select-user');
      userSelect.style.display = "none";
      const addButton = document.querySelector(".js-user-add");
      addButton.style.display = "none";
      response.users.forEach((user) => {
        const userListDetail = document.createElement("div");
        userListDetail.classList.add("member-list-item-detail");

        const userDetails = document.createElement("div");
        userDetails.classList.add("details");

        const userOptions = document.createElement("div");
        userOptions.classList.add("options");

        const userName = document.createElement("p");
        userName.classList.add("user-username");

        const spanUserName = document.createElement("span");
        spanUserName.classList.add(
          "user-username",
          "span-username",
          "u-inline-block"
        );
        spanUserName.innerHTML = user.userName;

        if (workspaceOwnerId === user.userId) {
          const removeSpan = document.createElement("span");
          removeSpan.innerHTML = "Owner";

          userOptions.appendChild(removeSpan);
        }

        if (user.userId === userId) {
          const removeButton = document.createElement("a");
          removeButton.classList.add("remove-button", "cursor-pointer");
          removeButton.setAttribute(
            "onclick",
            `removeUserFromWorkspace('${user.userId}')`
          );

          const removeSpan = document.createElement("span");
          removeSpan.innerHTML = "Remove";

          userOptions.appendChild(removeButton);
          removeButton.appendChild(removeSpan);
        }

        userListDetail.appendChild(userDetails);
        userListDetail.appendChild(userOptions);

        userDetails.appendChild(userName);
        userName.appendChild(spanUserName);

        userDetailsContainer.appendChild(userListDetail);
      });
    }
  } else {
    console.log(response);
    return;
  }
}

async function removeUserFromWorkspace(userId) {
  const workspaceId = localStorage.getItem("workspaceId");
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "workspaces",
      action: "removeUserFromWorkspace",
      userId: userId,
      workspaceId: workspaceId,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    location.reload();
  }
}

async function addUserToWorkspace(event) {
  event.preventDefault();
  const form = document.querySelector(".addUser");
  const workspaceId = localStorage.getItem("workspaceId");
  const userId = form.querySelector(".js-add-user-select").value;

  if (userId === "") {
    localStorage.setItem("toast", "false");
    localStorage.setItem("toastMessage", "Please choose a user");
    localStorage.setItem("toasttitle", "Failed");
    location.reload();
  }

  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "workspaces",
      action: "addUserToWorkspace",
      userId: userId,
      workspaceId: workspaceId,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    location.reload();
  }
}

async function deleteWorkspace(userId) {
  const workspaceId = localStorage.getItem("workspaceId");
  const call = await fetch(SYSTEM_ADDRESS + "ajax.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      scope: "workspaces",
      action: "deleteWorkspace",
      workspaceId: workspaceId,
    }),
  });
  const response = await call.json();
  if (response.status === "200") {
    window.location.href = `${SYSTEM_ADDRESS}workspaces/overview/${userId}`;
  }
}
