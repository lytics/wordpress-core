const element = window.wp.element;
const el = element.createElement;

const recommendationIcon = el(
  "svg",
  { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 96 88" },
  el("path", {
    d: "M62 16L52 40L28 50L52 60L62 84L72 60L96 50L72 40L62 16ZM16.5 27.5L22 44L27.5 27.5L44 22L27.5 16.5L22 0L16.5 16.5L0 22L16.5 27.5ZM25.5 70.5L22 60L18.5 70.5L8 74L18.5 77.5L22 88L25.5 77.5L36 74L25.5 70.5Z",
    fill: "black",
  })
);

export const blockConfig = {
  title: "Recommend Content",
  icon: recommendationIcon,
  category: "lytics",
  viewScript: "file:./lytics-recommendation-render.js",
  attributes: {
    recommendationKind: {
      type: "string",
      default: "individual",
    },
    interestEngine: {
      type: "string",
      default: "default",
    },
    collection: {
      type: "string",
      default: "content_with_images",
    },
    numberOfRecommendations: {
      type: "number",
      default: 3,
    },
    doNotShuffle: {
      type: "boolean",
      default: false,
    },
    includeRecentlyViewedContent: {
      type: "boolean",
      default: false,
    },
    showHeadline: {
      type: "boolean",
      default: true,
    },
    showImage: {
      type: "boolean",
      default: true,
    },
    showBody: {
      type: "boolean",
      default: true,
    },
    previewUID: {
      type: "string",
      default: "",
    },
    accountId: {
      type: "string",
      default: "",
    },
    contentCollections: {
      type: "object",
      default: {},
    },
    engines: {
      type: "object",
      default: {},
    },
  },
};
