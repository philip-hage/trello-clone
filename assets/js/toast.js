function openToastSuccess(title, message) {
  var toast = document.querySelector(".toast1");
  var toastTitle = document.querySelector(".toast__title");
  var toastP = document.querySelector(".toast__p");

  toastTitle.textContent = title;
  message = message.replace(/\+/g, " ");
  toastP.textContent = message;

  var openToastEvent = new CustomEvent("openToast");
  toast.dispatchEvent(openToastEvent);

  // Delete toast parameters from localStorage
  localStorage.removeItem("toast");
  localStorage.removeItem("toasttitle");
  localStorage.removeItem("toastMessage");
}

function openToastFailed(title, message) {
  var toast2 = document.querySelector(".toast2");
  var toastTitle2 = document.querySelector(".title2");
  var toastP2 = document.querySelector(".p2");

  toastTitle2.textContent = title;
  message = message.replace(/\+/g, " ");
  toastP2.textContent = message;

  var openToastEvent = new CustomEvent("openToast");
  toast2.dispatchEvent(openToastEvent);

  // Delete toast parameters from localStorage
  localStorage.removeItem("toast");
  localStorage.removeItem("toasttitle");
  localStorage.removeItem("toastMessage");
}

// Retrieve toast parameters from localStorage
const toastParams = {
  toast: localStorage.getItem("toast"),
  toasttitle: localStorage.getItem("toasttitle"),
  toastmessage: localStorage.getItem("toastMessage"),
};

// Check if 'toast' parameter is present and has a value of 'true'
if (
  toastParams.toast === "true" &&
  toastParams.toasttitle &&
  toastParams.toastmessage
) {
  openToastSuccess(toastParams.toasttitle, toastParams.toastmessage);
} else if (
  toastParams.toast === "false" &&
  toastParams.toasttitle &&
  toastParams.toastmessage
) {
  openToastFailed(toastParams.toasttitle, toastParams.toastmessage);
}
