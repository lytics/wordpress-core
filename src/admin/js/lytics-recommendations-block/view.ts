import {
  getRecommendation,
  RecommendationOptions,
} from "./api/get-recommendation";

class LyticsRender {
  _uid = "";

  constructor() {
    this.render();
  }

  placeholder = {
    title: "Example recommendation title",
    imageurls: [
      "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 600'%3E%3Crect width='600' height='600' fill='%23CCCCCC'/%3E%3Cg opacity='0.9'%3E%3Cpath d='M383.973 215.615H216.028C204.43 215.615 195.035 225.034 195.035 236.663V362.951C195.035 374.58 204.43 383.999 216.028 383.999H383.973C395.572 383.999 404.966 374.58 404.966 362.951V236.663C404.966 225.034 395.572 215.615 383.973 215.615ZM279.008 257.711C284.802 257.711 289.504 262.426 289.504 268.235C289.504 274.045 284.802 278.759 279.008 278.759C273.214 278.759 268.511 274.045 268.511 268.235C268.511 262.426 273.214 257.711 279.008 257.711ZM373.477 352.427H226.525L263.252 305.08L289.504 336.747L326.242 289.294L373.477 352.427Z' fill='white' fill-opacity='0.7'/%3E%3C/g%3E%3C/svg%3E",
    ],
    url: "#",
  };

  render = async () => {
    const recContainers = document.querySelectorAll('[id*="rec-container-"]');

    for (const element of Array.from(recContainers)) {
      // get rec-wrapper element within each rec-container
      const recWrapper = element.querySelector(".rec-wrapper");
      if (!recWrapper) {
        console.error('No element found with the class "rec-wrapper"');
        return;
      }

      const recommendationConfig = JSON.parse(
        atob(element.getAttribute("data-rec-config") || "")
      );

      const options = {
        accountId: recommendationConfig?.account_id,
        type: recommendationConfig?.recommendation_type,
        collection: recommendationConfig?.content_collection_id,
        engine: recommendationConfig?.interest_engine_id,
        // segment: recommendationConfig?.segment_id,
        // url: recommendationConfig?.url,
        noShuffle: recommendationConfig?.dont_shuffle_results,
        includeViewed: recommendationConfig?.include_viewed_content,
        maxItems: recommendationConfig?.number_of_recommendations,
        previewUID: recommendationConfig?.preview_uid,
        showHeadline: recommendationConfig?.show_headline,
        showImage: recommendationConfig?.show_image,
        showBody: recommendationConfig?.show_body,
      } as RecommendationOptions;

      // console.log("Recommendation Config:", recommendationConfig);

      recWrapper.innerHTML = "";

      let recs = [];
      if (options.previewUID) {
        console.log("Getting recommendations");
        recs = await getRecommendation(options);
      } else {
        console.log("Using placeholder");
        recs = Array.from(
          { length: recommendationConfig.number_of_recommendations },
          () => this.placeholder
        );
      }

      let count = 0;
      recs.forEach((rec: any) => {
        if (count >= recommendationConfig.number_of_recommendations) {
          return;
        }

        const recItem = document.createElement("div");

        recItem.classList.add(
          "flex-container",
          "flex-column",
          "justify-start",
          "align-stretch",
          "flex-1",
          "gap-small",
          "rec-item"
        );

        if (options.showImage) {
          if (
            (rec?.primaryimageurl !== undefined &&
              rec?.primaryimageurl !== "") ||
            (rec?.imageurls?.length > 0 && rec?.imageurls?.[0] !== "")
          ) {
            const img = document.createElement("img");
            img.classList.add("rec-img");
            img.alt = `Image of ${rec.title}`;
            img.src = rec.primaryimageurl || rec.imageurls[0];
            recItem.appendChild(img);
          } else {
            return;
          }
        }

        if (options.showHeadline) {
          const recTitle = document.createElement("div");
          recTitle.classList.add("rec-title");
          recItem.appendChild(recTitle);

          const titleLink = document.createElement("a");
          const protocol = window.location.protocol;
          titleLink.href = `${protocol}//${rec.url}`;
          titleLink.innerHTML = `<strong>${rec.title}</strong>`;
          recTitle.appendChild(titleLink);
        }

        if (options.showBody && rec.description) {
          const description = document.createElement("p");
          description.classList.add("rec-description");
          description.textContent = rec.description;
          recItem.appendChild(description);
        }

        recWrapper.appendChild(recItem);

        count++;
      });
    }
  };
}

// On page load
window.onload = function () {
  window._lytics_rec_render = new LyticsRender();
};
