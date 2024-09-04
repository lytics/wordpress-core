import LyticsRecommendationBlock from "@lytics/recommendation-block";

const LyticsRecommendationBlockInit = () => {
  LyticsRecommendationBlock.attach(document.body);
};

if (
  document.readyState === "complete" ||
  document.readyState === "interactive"
) {
  LyticsRecommendationBlockInit();
} else {
  window.addEventListener("load", LyticsRecommendationBlockInit);
}
