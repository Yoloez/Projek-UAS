//membuat drag and drop area untuk upload gambar
const dropArea = document.getElementById("drop-area");
const inputFile = document.getElementById("gambar");
const preview = document.getElementById("preview");

dropArea.addEventListener("click", () => inputFile.click());

inputFile.addEventListener("change", function () {
  showPreview(this.files[0]);
});

dropArea.addEventListener("dragover", (e) => {
  e.preventDefault();
  dropArea.classList.add("dragover");
});

dropArea.addEventListener("dragleave", () => {
  dropArea.classList.remove("dragover");
});

dropArea.addEventListener("drop", (e) => {
  e.preventDefault();
  dropArea.classList.remove("dragover");
  const file = e.dataTransfer.files[0];
  inputFile.files = e.dataTransfer.files; // Simulasikan input file
  showPreview(file);
});

function showPreview(file) {
  const reader = new FileReader();
  reader.onload = function (e) {
    preview.src = e.target.result;
    preview.style.display = "block";
  };
  reader.readAsDataURL(file);
}
