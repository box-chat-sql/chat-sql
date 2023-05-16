//notifiSwal('top-end','error','The answer is in error');
// icon status: success,error,warning,info,question
export default function (position = "", icon = "", title = "") {
    const Toast = Swal.mixin({
        toast: true,
        position: position,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    Toast.fire({
        icon: icon,
        title: title,
    }).then((result) => {
        return result;
    });
}
