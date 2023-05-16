import "./bootstrap";
import "bootstrap/scss/bootstrap.scss";
var messages = document.getElementById("content-messages");
const btnSend = document.getElementById("send-messages");
const loadRequest = document.getElementById("load_request");
const loadData = document.getElementById("load_data");
const boxLoadDataTable = document.getElementById("box_load_data_table");
const body = document.getElementById("chatbox__messages");
const deleteAll = document.getElementById("deleteAll");
const deleteSelected = document.getElementById("deleteSelected");

// disabled event click
handleDisabled(btnSend, "set");

// Add event click
btnSend.addEventListener("click", sendMessages);
if (deleteAll) deleteAll.addEventListener("click", handleDeleteAll);
if (deleteSelected)
    deleteSelected.addEventListener("click", handleDeleteSelected);

//function Send Messages
async function sendMessages() {
    if (messages.value.length > 0) {
        handleDisabled(messages, "set");
        loadRequest.classList.replace("d-none", "d-flex");
        appendChatboxMessages(
            showTextChat(messages.value, '', "question")
        );
        try {
            handleDisabled(btnSend, "set");
            const data = await queryAPI(messages.value);
            messages.value = "";
            if (data) {
                appendChatboxMessages(
                    showTextChat(String(data?.data), '')
                );
            }
        } catch (error) {
            console.log(error);
        } finally {
            loadRequest.classList.replace("d-flex", "d-none");
            handleDisabled(messages, "remove");
        }
    }
    if (messages.value == "") handleDisabled(btnSend, "set");
}

async function handleDeleteAll() {
    const confirms = await Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        return result;
    });

    if (!confirms.isConfirmed) return;

    const values = getValueSelected("all");

    getApi
        .deleteData("/chat/delete", { query: values })
        .then((data) => {
            notifiSwal("top-right", "success", data?.data);
            boxLoadDataTable.innerHTML = '';
        })
        .catch(() => {
            notifiSwal("top-right", "error", data?.data);
        });
}

async function handleDeleteSelected() {
    const confirms = await Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        return result;
    });

    if (!confirms.isConfirmed) return;

    const values = getValueSelected();
    if (!values) {
        return false;
    }
    getApi
        .deleteData("/chat/delete", { query: values })
        .then((data) => {
            notifiSwal("top-right", "success", data?.data);
            loadData.innerHTML = data?.html;
        })
        .catch(() => {
            notifiSwal("top-right", "error", data?.data);
        });
}

function getValueSelected(type = "") {
    let data = [];
    if (type == "all") {
        return ["all"];
    }
    const checkbox = document.getElementsByClassName("checkbox");

    for (let element of checkbox) {
        if (element.checked) {
            data.push(element.value);
        }
    }
    if (data.length == 0) {
        notifiSwal(
            "top-right",
            "error",
            "You cannot delete. Because the value is empty !!!"
        );
        return false;
    }
    return data;
}

//Check Value Messages
function checkValueMessages() {
    if (messages.value.length > 0) {
        handleDisabled(btnSend, "remove");
    }
}
//Show Text Chat
function showTextChat(value = "", time = "", kind = "") {
    let text = "";
    if (kind == "question") {
        text = `<div class="item_chatbox__messages ask">
            <span class="avatar"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 20 20" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path></svg></span>
            <div class="box_messages_content">
                <div class="item_chatbox__messages_time">${formatDateTime(
                    time
                )}</div>
                <div class="item_chatbox__messages_content">
                    ${value ? value : "Thank you I'll see you later"}
                </div>
            </div>
        </div>`;
        return text;
    }
    text = `<div class="item_chatbox__messages reply">
        <span class="avatar">AI</span>
        <div class="box_messages_content">
            <div class="item_chatbox__messages_time">${formatDateTime(
                time
            )}</div>
            <div class="item_chatbox__messages_content">
                ${value ? value : "What do you want to find with sql chat ?"}
            </div>
        </div>
    </div>`;

    return text;
}
//Render messages
function appendChatboxMessages(text = "") {
    body.insertAdjacentHTML("beforeend", text);
    srollBottomBoxChat(body);
}
//sroll Bottom Box Chat
function srollBottomBoxChat(elem) {
    elem.scrollTo({
        top: elem.scrollHeight,
        left: 0,
        behavior: "smooth",
    });
}

//Handle Cookie
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    document.cookie =
        name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
}

function formatDateTime(date) {
    if(date){
        return date;
    }

    const data = new Date();
    const hours = data.getHours().toString().padStart(2, '0');
    const minutes = data.getMinutes().toString().padStart(2, '0');
    const seconds = data.getSeconds().toString().padStart(2, '0');
    const day = data.getDate().toString().padStart(2, '0');
    const month = data.getMonth().toString().padStart(2, '0');
    const year = data.getFullYear().toString().padStart(2, '0');
    return `${
        hours
    }:${minutes}:${seconds} ${day}/${month}/${year}`;
}

//Handle Disabled
function handleDisabled(element, kind = "") {
    if (kind == "set") return element.setAttribute("disabled", "disabled");
    if (kind == "remove")
        return element.removeAttribute("disabled", "disabled");
}

function handleRemoveCookie(name) {
    eraseCookie(name);
    body.innerHTML = "";
    appendChatboxMessages(showTextChat());
    return;
}

//Init data
function initData() {
    const json = decodeURIComponent(getCookie("chatSQL"));
    const data = JSON.parse(json);
    if (body.children.length == 0 && data == null)
        appendChatboxMessages(showTextChat());
    data?.data.map((value) => {
        appendChatboxMessages(showTextChat(value.data, value.time, value.type));
    });
}

//DOMContentLoaded
document.addEventListener("DOMContentLoaded", function () {
    initData();
    const load = document.getElementById("loading_page");
    loadRequest.classList.add("d-none");
    load.remove();
});

//Get API
function queryAPI(value) {
    return getApi
        .queryData("/chat/query", { query: value })
        .then((data) => {
            return data;
        })
        .catch((err) => {
            console.log(err);
        });
}

//Define event window
window.checkValueMessages = checkValueMessages;
window.handleRemoveCookie = handleRemoveCookie;
