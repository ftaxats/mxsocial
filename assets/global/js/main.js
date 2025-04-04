(function () {
    "use strict";

    if (document.querySelector(".niceSelect")) {
        $(document).ready(function () {
            $('.niceSelect').niceSelect();
        });
    }

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    const fileUploader = document.getElementById('image-preview');
    if (fileUploader) {
        const images = document.querySelector('.preview-images');
        fileUploader.addEventListener('change', (e) => {
            e.preventDefault();
            const files = e.target.files;
            images.style.cssText = `display:flex; align-items:center;gap:15px; flex-wrap:wrap; margin-top:20px;`
            var children = "";
            for (var i = 0; i < files.length; ++i) {
                children += `
                    <div style='width:200px; height:auto;'>
                         <img alt='${files[i].type}'
                          src='${URL.createObjectURL(files[i])}'>
                    </div>
               `;
            }
            images.innerHTML = children;
        });
    }

}())
