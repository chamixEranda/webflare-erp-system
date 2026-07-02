function form_alert(formId, message) {
    return Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(document.getElementById(formId).action, {
                method: document.getElementById(formId).method,
                body: new FormData(document.getElementById(formId))
            }).then(response => {
                if (response.status == 200) {
                    toastr.success(response.message);
                    location.reload();
                }
                return response;
            });
        }
        return result.isConfirmed;
    });
}

$('#logout-link').on('click', function(e) {
    e.preventDefault();
    $('#logout-form').submit();
});