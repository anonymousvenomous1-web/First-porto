const menuButton = document.querySelector(".menu-toggle");
const navigation = document.querySelector("#site-navigation");
const parallaxLayers = document.querySelectorAll("[data-parallax]");
const reducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)");
const listenToReducedMotion = (listener) => {
  if (typeof reducedMotion.addEventListener === "function") {
    reducedMotion.addEventListener("change", listener);
  } else {
    reducedMotion.addListener(listener);
  }
};
const pointerLight = document.querySelector(".pointer-light");
const projectArtToggle = document.querySelector(".project-art-toggle");
const intuitionArt = document.querySelector(".project-art-intuition-art");
const intuitionCollage = document.querySelector(".project-art-collage");
const collageGallery = document.querySelector(".project-art-gallery");
const imageLightbox = document.querySelector(".collage-lightbox");
const lightboxImage = imageLightbox?.querySelector(".collage-lightbox-image");

const protectedImageSelector = "img, picture, svg, canvas";

const protectImageElement = (element) => {
  if (!(element instanceof Element)) return;

  element.setAttribute("draggable", "false");
  element.addEventListener("contextmenu", (event) => {
    event.preventDefault();
  }, { passive: false });
  element.addEventListener("dragstart", (event) => {
    event.preventDefault();
  }, { passive: false });
};

for (const image of document.images) {
  protectImageElement(image);
}

document.querySelectorAll(protectedImageSelector).forEach((element) => {
  protectImageElement(element);
});

const preventImageContextMenu = (event) => {
  const target = event.target;
  if (target instanceof Element && target.closest(protectedImageSelector)) {
    event.preventDefault();
    event.stopPropagation();
  }
};

document.addEventListener("contextmenu", preventImageContextMenu, { capture: true });

document.addEventListener("dragstart", (event) => {
  if (event.target instanceof Element && event.target.closest(protectedImageSelector)) {
    event.preventDefault();
  }
}, { capture: true });

if (projectArtToggle) {
  const illustration = projectArtToggle.querySelector(".project-art-illustration");
  const photo = projectArtToggle.querySelector(".project-art-photo");

  if (illustration && photo) {
    projectArtToggle.addEventListener("click", () => {
      const showingPhoto = projectArtToggle.classList.toggle("is-photo");
      projectArtToggle.setAttribute("aria-label", showingPhoto ? "Show artwork" : "Show progress photo");
      illustration.setAttribute("aria-hidden", String(showingPhoto));
      photo.setAttribute("aria-hidden", String(!showingPhoto));
    });
  }
}

if (intuitionArt && intuitionCollage && collageGallery) {
  const returnToArtwork = collageGallery.querySelector(".project-art-return");
  const setInert = (element, isInert) => {
    if ("inert" in element) element.inert = isInert;
  };

  const setCollageView = (showCollage) => {
    intuitionCollage.classList.toggle("is-collage", showCollage);
    intuitionArt.setAttribute("aria-hidden", String(showCollage));
    intuitionArt.setAttribute("aria-expanded", String(showCollage));
    setInert(intuitionArt, showCollage);
    collageGallery.setAttribute("aria-hidden", String(!showCollage));
    setInert(collageGallery, !showCollage);
    intuitionArt.tabIndex = showCollage ? -1 : 0;
    for (const control of collageGallery.querySelectorAll("button")) {
      control.tabIndex = showCollage ? 0 : -1;
    }
    const focusTarget = showCollage ? returnToArtwork : intuitionArt;
    if (focusTarget && getComputedStyle(focusTarget).visibility === "visible") {
      focusTarget.focus({ preventScroll: true });
    }
  };

  intuitionArt.addEventListener("click", () => setCollageView(true));
  returnToArtwork?.addEventListener("click", () => setCollageView(false));
}

if (collageGallery && imageLightbox instanceof HTMLDialogElement && lightboxImage instanceof HTMLImageElement) {
  collageGallery.addEventListener("click", (event) => {
    if (!(event.target instanceof Element)) return;
    const imageButton = event.target.closest(".collage-image-button");
    const thumbnail = imageButton?.querySelector("img");
    if (!thumbnail) return;

    lightboxImage.src = thumbnail.src;
    lightboxImage.alt = imageButton.getAttribute("aria-label")?.replace(/^Expand (artwork|photo): /, "") ?? "";
    imageLightbox.showModal();
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && imageLightbox.open) {
      imageLightbox.close();
    }
  });

  imageLightbox.addEventListener("click", (event) => {
    if (event.target === imageLightbox) imageLightbox.close();
  });

  imageLightbox.addEventListener("close", () => {
    lightboxImage.removeAttribute("src");
    lightboxImage.alt = "";
  });
}

if (menuButton && navigation) {
  menuButton.addEventListener("click", () => {
    const isOpen = menuButton.getAttribute("aria-expanded") === "true";
    menuButton.setAttribute("aria-expanded", String(!isOpen));
    navigation.classList.toggle("is-open", !isOpen);
  });

  navigation.addEventListener("click", (event) => {
    if (event.target instanceof HTMLAnchorElement) {
      menuButton.setAttribute("aria-expanded", "false");
      navigation.classList.remove("is-open");
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && menuButton.getAttribute("aria-expanded") === "true") {
      menuButton.setAttribute("aria-expanded", "false");
      navigation.classList.remove("is-open");
      menuButton.focus();
    }
  });
}

if (parallaxLayers.length > 0) {
  let frameRequested = false;

  const moveArtwork = () => {
    if (reducedMotion.matches) {
      for (const layer of parallaxLayers) {
        layer.style.removeProperty("--parallax-y");
      }
      document.body.style.removeProperty("--atmosphere-x");
      document.body.style.removeProperty("--atmosphere-y");
      document.body.style.removeProperty("--bokeh-x");
      document.body.style.removeProperty("--bokeh-y");
      for (const layer of parallaxLayers) {
        layer.style.removeProperty("--parallax-x");
      }
      frameRequested = false;
      return;
    }

    const hero = document.querySelector(".hero");
    if (!hero) return;

    const bounds = hero.getBoundingClientRect();
    const progress = Math.max(0, Math.min(1, window.scrollY / Math.max(bounds.height, 1)));
    document.body.style.setProperty("--atmosphere-x", `${progress * 3}%`);
    document.body.style.setProperty("--atmosphere-y", `${progress * 2.4}%`);
    document.body.style.setProperty("--bokeh-x", `${progress * -2.4}%`);
    document.body.style.setProperty("--bokeh-y", `${progress * -3.5}%`);

    for (const layer of parallaxLayers) {
      const depth = Number(layer.dataset.parallax);
      const horizontalDepth = Number(layer.dataset.parallaxX ?? 0);
      layer.style.setProperty("--parallax-y", `${progress * depth * 200}px`);
      layer.style.setProperty("--parallax-x", `${progress * horizontalDepth * 140}px`);
    }

    frameRequested = false;
  };

  window.addEventListener("scroll", () => {
    if (!reducedMotion.matches && !frameRequested) {
      window.requestAnimationFrame(moveArtwork);
      frameRequested = true;
    }
  }, { passive: true });

  listenToReducedMotion(moveArtwork);

  if (!reducedMotion.matches) moveArtwork();
}

if (pointerLight) {
  let pointerFrameRequested = false;
  let pointerX = 0;
  let pointerY = 0;

  const followPointer = () => {
    pointerLight.style.setProperty("--pointer-x", `${pointerX}px`);
    pointerLight.style.setProperty("--pointer-y", `${pointerY}px`);
    pointerFrameRequested = false;
  };

  window.addEventListener("pointermove", (event) => {
    if (reducedMotion.matches || (event.pointerType !== "mouse" && event.pointerType !== "pen")) return;

    pointerX = event.clientX;
    pointerY = event.clientY;
    pointerLight.classList.add("is-visible");

    if (!pointerFrameRequested) {
      window.requestAnimationFrame(followPointer);
      pointerFrameRequested = true;
    }
  }, { passive: true });

  document.documentElement.addEventListener("pointerleave", () => {
    pointerLight.classList.remove("is-visible");
  });

  listenToReducedMotion(() => {
    if (reducedMotion.matches) {
      pointerLight.classList.remove("is-visible");
      pointerLight.style.removeProperty("--pointer-x");
      pointerLight.style.removeProperty("--pointer-y");
    }
  });
}
