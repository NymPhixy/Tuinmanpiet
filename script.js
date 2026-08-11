/* ============================================
   TUINMAN PIET - JAVASCRIPT
   ============================================ */

/**
 * HAMBURGER MENU
 * Toggle het mobiele navigatiemenu
 */
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.querySelector(".menu-toggle");
  const nav = document.querySelector(".nav");
  const navLinks = document.querySelectorAll(".nav-link");

  /**
   * Toggle het menu open/dicht
   */
  menuToggle.addEventListener("click", function () {
    const isOpen = nav.classList.contains("active");

    if (isOpen) {
      closeMenu();
    } else {
      openMenu();
    }
  });

  /**
   * Menu openen
   */
  function openMenu() {
    nav.classList.add("active");
    menuToggle.classList.add("active");
    menuToggle.setAttribute("aria-expanded", "true");
  }

  /**
   * Menu sluiten
   */
  function closeMenu() {
    nav.classList.remove("active");
    menuToggle.classList.remove("active");
    menuToggle.setAttribute("aria-expanded", "false");
  }

  /**
   * Menu sluiten wanneer op een navigatielink wordt geklikt
   * Dit zorgt voor betere UX op mobiel
   */
  navLinks.forEach((link) => {
    link.addEventListener("click", function () {
      closeMenu();
    });
  });

  /**
   * Menu sluiten wanneer buiten het menu wordt geklikt
   * Dit voelt natuurlijker voor gebruikers
   */
  document.addEventListener("click", function (event) {
    const isMenuOpen = nav.classList.contains("active");
    const isClickInsideMenu = nav.contains(event.target);
    const isClickOnToggle = menuToggle.contains(event.target);

    if (isMenuOpen && !isClickInsideMenu && !isClickOnToggle) {
      closeMenu();
    }
  });

  /**
   * ACTIEVE NAVIGATIELINK HIGHLIGHTEN
   * Voeg een actieve staat toe aan de navigatielink die overeenkomt met de huidige sectie
   */
  function highlightActiveLink() {
    const sections = document.querySelectorAll("section");

    sections.forEach((section) => {
      const sectionTop = section.offsetTop;
      const sectionHeight = section.clientHeight;
      const currentScroll = window.scrollY + 100; // 100px offset voor sticky header

      if (
        currentScroll >= sectionTop &&
        currentScroll < sectionTop + sectionHeight
      ) {
        // Verwijder actieve klasse van alle links
        navLinks.forEach((link) => link.classList.remove("active"));

        // Voeg actieve klasse toe aan de juiste link
        const activeLink = document.querySelector(
          `.nav-link[href="#${section.id}"]`,
        );
        if (activeLink) {
          activeLink.classList.add("active");
        }
      }
    });
  }

  /**
   * Highlight actieve link bij paginaladen
   */
  highlightActiveLink();

  /**
   * Highlight actieve link wanneer gebruiker scrollt
   */
  window.addEventListener("scroll", highlightActiveLink);

  /**
   * SMOOTH SCROLLING FALLBACK
   * Voor browsers die CSS scroll-behavior niet ondersteunen
   */
  navLinks.forEach((link) => {
    link.addEventListener("click", function (event) {
      const href = this.getAttribute("href");
      if (href && href.startsWith("#")) {
        event.preventDefault();
        const element = document.querySelector(href);

        if (element) {
          element.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      }
    });
  });

  /**
   * TELEFOONNUMMERFORMATTERING
   * Maak het telefoonnummer opmaak-vriendelijk
   */
  const telLink = document.querySelector('a[href^="tel:"]');
  if (telLink) {
    const tel = "+31629000000";
    telLink.setAttribute("href", `tel:${tel}`);
  }

  /**
   * IMAGE LAZY LOADING (optioneel)
   * Voeg een eenvoudige placeholder-afbeelding in met alt-tekst
   */
  const images = document.querySelectorAll("img");
  images.forEach((img) => {
    // Zorg dat alle afbeeldingen alt-tekst hebben (al ingesteld in HTML)
    if (!img.alt) {
      img.alt = "Afbeelding van Tuinman Piet project";
    }
  });

  /**
   * SCROLLBAR VERSIERSELEN
   * Voeg een voortgang-indicator toe via CSS (kan ook in CSS)
   */
  window.addEventListener("scroll", function () {
    const totalHeight =
      document.documentElement.scrollHeight -
      document.documentElement.clientHeight;
    const scrolled = (window.scrollY / totalHeight) * 100;

    // Dit kan worden gebruikt voor een voortgangsbalk indien gewenst
    // Momenteel wordt dit hier berekend maar niet gebruikt
  });

  /**
   * VOORKEUR VOOR BEWEGING RESPECTEREN
   * Controleer of gebruiker liever geen animaties ziet
   */
  const prefersReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
  );
  if (prefersReducedMotion.matches) {
    // CSS handelt dit af via @media (prefers-reduced-motion: reduce)
    // Dit is hier ter info
  }

  /**
   * DARK MODE SUPPORT (optioneel)
   * Controleer of gebruiker te voorkeur heeft voor dark mode
   */
  const prefersDarkMode = window.matchMedia("(prefers-color-scheme: dark)");
  if (prefersDarkMode.matches) {
    // Momenteel gebruiken we geen dark mode, maar dit kan eenvoudig worden toegevoegd
    // document.body.classList.add('dark-mode');
  }
});

/**
 * PROJECTS DATA & FILTERING
 * Laad projecten uit JSON en render ze op de projectenpagina
 */

// Globale projects array
let allProjects = [];
let currentFilter = "all";

/**
 * Load projects from JSON file
 */
async function loadProjects() {
  try {
    const response = await fetch("data/projects.json");
    if (!response.ok) {
      throw new Error("Failed to load projects");
    }
    const data = await response.json();
    allProjects = normalizeProjects(data);

    // Render projects op projecten.html
    if (document.getElementById("projects-grid")) {
      renderProjects(allProjects);
      setupFilterButtons();
    }

    // Render preview op index.html
    if (document.getElementById("portfolio-preview-grid")) {
      renderPortfolioPreview();
    }

    // Render complete fotogalerij op index.html
    if (document.getElementById("gallery-grid")) {
      renderPhotoGallery();
    }
  } catch (error) {
    console.error("Error loading projects:", error);
    const errorElement = document.getElementById("projects-error");
    if (errorElement) {
      errorElement.style.display = "block";
    }
  }
}

/**
 * Render all projects or filtered projects
 */
function renderProjects(projects) {
  const grid = document.getElementById("projects-grid");
  if (!grid) return;

  grid.innerHTML = "";

  if (projects.length === 0) {
    grid.innerHTML =
      '<p style="grid-column: 1/-1; text-align: center; padding: 2rem;">Geen projecten gevonden.</p>';
    return;
  }

  projects.forEach((project) => {
    const card = createProjectCard(project);
    grid.appendChild(card);
  });
}

/**
 * Generate fallback placeholder image
 */
function getPlaceholderImage() {
  return "assets/img/projects/placemaker.png";
}

/**
 * Create a project card element
 */
function createProjectCard(project) {
  const article = document.createElement("article");
  article.className = "portfolio-card";
  article.innerHTML = `
    <div class="portfolio-image">
      <img src="${project.coverImage}" alt="${project.title}" data-project-id="${project.id}" class="project-img">
    </div>
    <div class="portfolio-content">
      <span class="portfolio-label">${project.category}</span>
      <h3>${project.title}</h3>
      <p>${project.description}</p>
    </div>
  `;

  // Add error handler with fallback placeholder
  const img = article.querySelector("img");
  img.addEventListener("error", function () {
    this.src = getPlaceholderImage();
  });

  article.addEventListener("click", () => {
    openProjectModal(project);
  });

  return article;
}

/**
 * Render portfolio preview on homepage (3 items)
 */
function renderPortfolioPreview() {
  const previewGrid = document.getElementById("portfolio-preview-grid");
  if (!previewGrid) return;

  previewGrid.innerHTML = "";

  // Show only first 3 projects
  const previewProjects = allProjects.slice(0, 3);

  previewProjects.forEach((project) => {
    const card = createProjectCard(project);
    previewGrid.appendChild(card);
  });
}

/**
 * Render all project photos in a responsive gallery
 */
function renderPhotoGallery() {
  const galleryGrid = document.getElementById("gallery-grid");
  if (!galleryGrid) return;

  galleryGrid.innerHTML = "";

  const galleryItems = [];

  allProjects.forEach((project) => {
    const images =
      project.images && project.images.length > 0
        ? project.images
        : [project.coverImage];

    images.forEach((image, index) => {
      galleryItems.push({
        src: image,
        title: project.title,
        category: project.category,
        projectId: project.id,
        imageIndex: index + 1,
      });
    });
  });

  galleryItems.forEach((item) => {
    const galleryButton = document.createElement("button");
    galleryButton.type = "button";
    galleryButton.className = "gallery-tile";
    galleryButton.setAttribute(
      "aria-label",
      `${item.title}, foto ${item.imageIndex}`,
    );

    galleryButton.innerHTML = `
      <div class="gallery-tile-image">
        <img src="${item.src}" alt="${item.title}" loading="lazy">
      </div>
      <div class="gallery-tile-content">
        <span class="gallery-tile-label">${item.category}</span>
        <strong>${item.title}</strong>
      </div>
    `;

    const img = galleryButton.querySelector("img");
    img.addEventListener("error", function () {
      this.src = getPlaceholderImage();
    });

    galleryButton.addEventListener("click", () => {
      const project = allProjects.find(
        (projectItem) => projectItem.id === item.projectId,
      );
      if (project) {
        openProjectModal(project);
      }
    });

    galleryGrid.appendChild(galleryButton);
  });
}

/**
 * Setup filter buttons
 */
function setupFilterButtons() {
  const filterButtons = document.querySelectorAll(".filter-btn");

  filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const filter = button.getAttribute("data-filter");
      filterProjects(filter);

      // Update active button
      filterButtons.forEach((btn) => btn.classList.remove("active"));
      button.classList.add("active");
    });
  });
}

/**
 * Filter projects by category
 */
function filterProjects(category) {
  currentFilter = category;

  if (category === "all") {
    renderProjects(allProjects);
  } else {
    const filtered = allProjects.filter(
      (project) => project.category === category,
    );
    renderProjects(filtered);
  }
}

/**
 * PROJECT MODAL / LIGHTBOX
 * Open project detail modal
 */
function openProjectModal(project) {
  const modal = document.getElementById("project-modal");
  if (!modal) return;

  // Set modal content
  document.getElementById("modal-title").textContent = project.title;
  document.getElementById("modal-category").textContent = project.category;
  document.getElementById("modal-location").textContent = project.location;
  document.getElementById("modal-year").textContent = project.year;
  document.getElementById("modal-description").textContent =
    project.description;

  // Set main image with error handling
  const modalImage = document.getElementById("modal-image");
  const mainImg = document.createElement("img");
  mainImg.src = project.coverImage;
  mainImg.alt = project.title;
  mainImg.addEventListener("error", function () {
    this.src = getPlaceholderImage();
  });
  modalImage.innerHTML = "";
  modalImage.appendChild(mainImg);

  // Set gallery if there are extra images
  const modalGallery = document.getElementById("modal-gallery");
  if (project.images && project.images.length > 0) {
    modalGallery.style.display = "block";
    const galleryImages = document.getElementById("modal-gallery-images");
    galleryImages.innerHTML = "";

    project.images.forEach((image) => {
      const galleryItem = document.createElement("div");
      galleryItem.className = "gallery-image";
      const galleryImg = document.createElement("img");
      galleryImg.src = image;
      galleryImg.alt = project.title;
      galleryImg.addEventListener("error", function () {
        this.src = getPlaceholderImage();
      });
      galleryItem.appendChild(galleryImg);
      galleryImages.appendChild(galleryItem);
    });
  } else {
    modalGallery.style.display = "none";
  }

  // Show modal
  modal.classList.add("active");
  modal.setAttribute("aria-hidden", "false");
  document.body.style.overflow = "hidden";
}

/**
 * Close project modal
 */
function closeProjectModal() {
  const modal = document.getElementById("project-modal");
  if (!modal) return;

  modal.classList.remove("active");
  modal.setAttribute("aria-hidden", "true");
  document.body.style.overflow = "";
}

/**
 * Setup modal close button and overlay
 */
function setupModalControls() {
  const modal = document.getElementById("project-modal");
  const modalClose = document.querySelector(".modal-close");
  const modalOverlay = document.getElementById("modal-overlay");

  if (modalClose) {
    modalClose.addEventListener("click", closeProjectModal);
  }

  if (modalOverlay) {
    modalOverlay.addEventListener("click", closeProjectModal);
  }

  // Close modal with Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal && modal.classList.contains("active")) {
      closeProjectModal();
    }
  });
}

/**
 * Initialize projects functionality when DOM is ready
 */
document.addEventListener("DOMContentLoaded", function () {
  loadProjects();
  setupModalControls();
  initializeCarousel();
});

/**
 * CAROUSEL FUNCTIONALITY
 * Dynamisch carousel voor projectfoto's
 */

let carouselState = {
  currentIndex: 0,
  items: [],
  totalItems: 0,
};

/**
 * Initialize the carousel
 */
function initializeCarousel() {
  const carouselTrack = document.getElementById("carousel-track");
  const prevBtn = document.getElementById("carousel-prev");
  const nextBtn = document.getElementById("carousel-next");

  if (!carouselTrack || !allProjects.length) return;

  // Flatten all images from all projects
  carouselState.items = [];
  allProjects.forEach((project) => {
    if (project.images && project.images.length > 0) {
      project.images.forEach((image) => {
        carouselState.items.push({
          src: image,
          title: project.title,
        });
      });
    }
  });

  carouselState.totalItems = carouselState.items.length;

  if (carouselState.totalItems === 0) return;

  // Render carousel items
  renderCarouselItems();

  // Setup indicators
  setupCarouselIndicators();

  // Setup navigation buttons
  if (prevBtn && nextBtn) {
    prevBtn.addEventListener("click", () => prevCarouselItem());
    nextBtn.addEventListener("click", () => nextCarouselItem());

    // Keyboard navigation
    document.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") prevCarouselItem();
      if (e.key === "ArrowRight") nextCarouselItem();
    });
  }

  // Auto-advance carousel every 5 seconds
  setInterval(() => {
    nextCarouselItem();
  }, 5000);
}

/**
 * Render carousel items
 */
function renderCarouselItems() {
  const carouselTrack = document.getElementById("carousel-track");
  carouselTrack.innerHTML = "";

  carouselState.items.forEach((item) => {
    const carouselItem = document.createElement("div");
    carouselItem.className = "carousel-item";

    const img = document.createElement("img");
    img.src = item.src;
    img.alt = item.title;
    img.addEventListener("error", function () {
      this.src = getPlaceholderImage();
    });

    carouselItem.appendChild(img);
    carouselTrack.appendChild(carouselItem);
  });

  updateCarouselPosition();
}

/**
 * Setup carousel indicators
 */
function setupCarouselIndicators() {
  const indicatorsContainer = document.getElementById("carousel-indicators");
  indicatorsContainer.innerHTML = "";

  carouselState.items.forEach((_, index) => {
    const indicator = document.createElement("button");
    indicator.className = "carousel-indicator";
    if (index === 0) indicator.classList.add("active");

    indicator.addEventListener("click", () => {
      carouselState.currentIndex = index;
      updateCarouselPosition();
      updateIndicators();
    });

    indicatorsContainer.appendChild(indicator);
  });
}

/**
 * Move to next carousel item
 */
function nextCarouselItem() {
  carouselState.currentIndex =
    (carouselState.currentIndex + 1) % carouselState.totalItems;
  updateCarouselPosition();
  updateIndicators();
}

/**
 * Move to previous carousel item
 */
function prevCarouselItem() {
  carouselState.currentIndex =
    (carouselState.currentIndex - 1 + carouselState.totalItems) %
    carouselState.totalItems;
  updateCarouselPosition();
  updateIndicators();
}

/**
 * Update carousel position based on current index
 */
function updateCarouselPosition() {
  const carouselTrack = document.getElementById("carousel-track");
  const offset = -carouselState.currentIndex * 100;
  carouselTrack.style.transform = `translateX(${offset}%)`;
}

/**
 * Update active indicator
 */
function updateIndicators() {
  const indicators = document.querySelectorAll(".carousel-indicator");
  indicators.forEach((indicator, index) => {
    indicator.classList.remove("active");
    if (index === carouselState.currentIndex) {
      indicator.classList.add("active");
    }
  });
}

/**
 * SUPPORT VOOR ROOT ARRAY EN ROOT OBJECT PROJECTSTRUCTUREN
 * Ondersteunt beide formats:
 * - Directe array: [{...}, {...}]
 * - Root object: { projects: [{...}, {...}] }
 */
function normalizeProjects(data) {
  if (Array.isArray(data)) {
    return data;
  }
  if (
    data &&
    typeof data === "object" &&
    data.projects &&
    Array.isArray(data.projects)
  ) {
    return data.projects;
  }
  return [];
}

/**
 * CONSOLEBERICHT
 * Een klein paaseitje voor developers
 */
console.log(
  "%cTuinman Piet Website",
  "font-size: 20px; font-weight: bold; color: #FF9800;",
);
console.log(
  "%cGemaakt met HTML, CSS en JavaScript",
  "font-size: 12px; color: #4CAF50;",
);
console.log(
  "%cAlles is responsive, toegankelijk en optimaal!",
  "font-size: 12px; color: #333;",
);
