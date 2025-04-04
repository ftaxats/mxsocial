(function () {
  "use strict";

  function sanitizeText(text) {
    var element = document.createElement('div');
    element.innerText = text;
    return element.innerHTML;
  }

  function sanitizeFileName(fileName) {
    return sanitizeText(fileName);
  }

  function handleFileUpload(files) {
    var preview = $(".file-list");
    $(files).each(function (i, file) {
        var reader = new FileReader();
        uploadedFiles.push(file);

        reader.onload = function (e) {
            if (file.type.startsWith('image/')) {
                preview.append(
                    `<li>
                        <span class="remove-list" data-name="${sanitizeFileName(file.name)}">
                            <i class="bi bi-x-circle"></i>
                        </span>
                        <img src="${e.target.result}" alt="${sanitizeFileName(file.name)}" />
                    </li>`
                );
            } else if (file.type.startsWith('video/')) {
              preview.append(
                `<li>
                      <span class="remove-list" data-name="${sanitizeFileName(file.name)}">
                        <i class="bi bi-x-circle"></i>
                    </span>
                    <video width="150" controls>
                      <source src="${URL.createObjectURL(file)}">
                    </video>
                </li>`
              );
            }
        };

        reader.readAsDataURL(file);
    });
  }

  function previewAppear(){
    document.querySelectorAll(".platform-note").forEach((note) => {
      note.classList.add('d-none');
    });

    document.querySelectorAll(".social-preview-body").forEach((preview) => {
      preview.classList.remove('d-none');
    });
  }

  var uploadedFiles = [];
  var fileInput;
  $(".upload-filed input").on("change", function () {
    fileInput = this;
    uploadedFiles = Array.from(uploadedFiles);
    previewAppear();   
    handleFileUpload(fileInput.files);
    handelFilePreview(fileInput.files);
    uploadedFiles = createFileList(uploadedFiles);
    fileInput.files = uploadedFiles;
  });

  $(document).on('click', '.remove-list', function (e) {
    e.preventDefault()
    var fileName = $(this).data("name");
    $(this).parent().remove();

    var selectedFiles = Array.from(uploadedFiles);

    selectedFiles = selectedFiles.filter(function (file) {
      return file.name !== fileName;
    });

    var newFileList = new DataTransfer();
    selectedFiles.forEach(function (file) {
      newFileList.items.add(file);
    });

    uploadedFiles = newFileList.files;
    fileInput.files = newFileList.files;
    handelFilePreview(uploadedFiles, true);
  });

  function handelFilePreview(files, remove = false) {
    var captionImgs = document.querySelectorAll(".caption-imgs");

    captionImgs.forEach((imageWrap) => {
      imageWrap.innerHTML = "";
      let count = 0;
      Array.from(files).forEach((file) => {

        files.length === 0
          ? imageWrap.classList.add("placeholder-img")
          : imageWrap.classList.remove("placeholder-img");
          
        files.length === 1
          ? imageWrap.classList.add("imgOne")
          : imageWrap.classList.remove("imgOne");

        files.length === 2
          ? imageWrap.classList.add("imgTwo")
          : imageWrap.classList.remove("imgTwo");

        if (count < 3) {
          const reader = new FileReader();
          reader.addEventListener("load", (e) => {
            if (remove) {
              if (file.name.endsWith('.mp4')) {
                imageWrap.innerHTML += `
                  <div class="caption-img">
                    <video width="100%" controls>
                      <source src="${URL.createObjectURL(file)}">
                    </video>
                    ${count === 3 && files.length > 3
                        ? `<div class="overlay"><p>+${files.length - 3}</p></div>`
                        : ""}
                  </div>
                `;
              } else {
                imageWrap.innerHTML += `
                  <div class="caption-img">
                    <img src="${e.target.result}" alt="${sanitizeFileName(file.name)}" />
                    ${count === 3 && files.length > 3
                        ? `<div class="overlay"><p>+${files.length - 3}</p></div>`
                        : ""}
                  </div>
                `;
              }
            } else {
              if (file.type.startsWith('image/')) {
                imageWrap.innerHTML += `
                  <div class="caption-img">
                    <img src="${e.target.result}" alt="${sanitizeFileName(file.name)}" />
                    ${count === 3 && files.length > 3
                        ? `<div class="overlay"><p>+${files.length - 3}</p></div>`
                        : ""}
                  </div>
                `;
              } else if (file.type.startsWith('video/')) {
                imageWrap.innerHTML += `
                  <div class="caption-img">
                    <video width="100%" controls>
                      <source src="${URL.createObjectURL(file)}">
                    </video>
                    ${count === 3 && files.length > 3
                        ? `<div class="overlay"><p>+${files.length - 3}</p></div>`
                        : ""}
                  </div>
                `;
              }
            }
          });
          reader.readAsDataURL(file);
          count++;
        } else {
          return;
        }

      });
    });
  }


  function createFileList(fileItems) {
    const dataTransfer = new DataTransfer();

    fileItems.forEach((fileItem) => {
      const file = new File([fileItem], fileItem.name);
      dataTransfer.items.add(file);
    });
    return dataTransfer.files;
  }

  const composeBody = document.querySelector(".compose-body");
  if (composeBody) {
    composeBody.addEventListener("mouseover", () => {
      composeBody.classList.add("focused");
    });

    composeBody.addEventListener("mouseout", () => {
      composeBody.classList.remove("focused");
    });
  }

  const composeWrapper = document.querySelector(".compose-wrapper");
  if (composeWrapper) {
    const composeInput = composeWrapper.querySelector(".compose-input"),
      allCaptionText = composeWrapper.querySelectorAll(".caption-text"),
      inputLink = composeWrapper.querySelector("#link"),
      captionLink = composeWrapper.querySelectorAll(".caption-link");

      
      const updateCaptionText = (value) => {
        const sanitizedValue = sanitizeText(value);
        allCaptionText.forEach((textElement) => {
          const highlightedText = sanitizedValue.replace(/(#\w+)/g, '<span class="hashtag">$1</span>');
          textElement.innerHTML = highlightedText;
        });
      };
      

    composeInput.addEventListener("keyup", (e) => {
      previewAppear(); 
      updateCaptionText(e.target.value);
    });

    composeInput.addEventListener("input", (e) => {
      updateCaptionText(e.target.value);
    });

    inputLink.addEventListener("input", (e) => {

      previewAppear(); 
      captionLink.forEach((link) => {
        if (e.target.value) {
          link.classList.remove("d-none");

          link.innerHTML = `
             <a href="javascript:void(0)">
               <span class="link-domin">
                 ${sanitizeText(e.target.value)}
               </span>
               <h6>Preview</h6>
               <p>
                 Preview approximates how your content will
                 display when published.
               </p>
             </a>
           `;
        } else {
          link.classList.add("d-none");
        }
      });
    });
  }
})();
