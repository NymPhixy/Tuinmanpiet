const filterButtons = document.querySelectorAll(".filter-btn");
const projectCards = document.querySelectorAll(".project-card");

if (filterButtons.length && projectCards.length) {
  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const selectedFilter = button.dataset.filter;

      filterButtons.forEach((btn) => btn.classList.remove("active"));
      button.classList.add("active");

      projectCards.forEach((card) => {
        const cardCategory = card.dataset.category;

        if (selectedFilter === "Alles" || selectedFilter === cardCategory) {
          card.classList.remove("is-hidden");
        } else {
          card.classList.add("is-hidden");
        }
      });
    });
  });
}
const galleryItems = document.querySelectorAll(".gallery-item");
const lightbox = document.querySelector("#lightbox");
const lightboxImage = document.querySelector("#lightboxImage");
const lightboxTitle = document.querySelector("#lightboxTitle");
const closeLightboxButtons = document.querySelectorAll("[data-close-lightbox]");

if (galleryItems.length && lightbox && lightboxImage && lightboxTitle) {
  galleryItems.forEach((item) => {
    item.addEventListener("click", () => {
      const image = item.dataset.image;
      const title = item.dataset.title;

      lightboxImage.src = image;
      lightboxImage.alt = title;
      lightboxTitle.textContent = title;

      lightbox.classList.add("is-open");
      lightbox.setAttribute("aria-hidden", "false");
      document.body.style.overflow = "hidden";
    });
  });

  closeLightboxButtons.forEach((button) => {
    button.addEventListener("click", closeLightbox);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && lightbox.classList.contains("is-open")) {
      closeLightbox();
    }
  });
}

function closeLightbox() {
  if (!lightbox || !lightboxImage || !lightboxTitle) {
    return;
  }

  lightbox.classList.remove("is-open");
  lightbox.setAttribute("aria-hidden", "true");
  lightboxImage.src = "";
  lightboxImage.alt = "";
  lightboxTitle.textContent = "";
  document.body.style.overflow = "";
}
