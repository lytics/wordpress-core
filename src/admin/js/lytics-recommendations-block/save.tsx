import React from "react";
import "./editor.scss";
import { RecommendationOptions } from "./api/get-recommendation";

// WordPress dependencies
const element = window.wp.element;
const editor = window.wp.blockEditor;
const components = window.wp.components;

// State management
let hasLoaded = false;
let rendered: React.JSX.Element;
let renderedOptions: RecommendationOptions;

const placeholderItem = {
  title: "Title will go here",
  imageurls: [
    "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 600'%3E%3Crect width='600' height='600' fill='%23CCCCCC'/%3E%3Cg opacity='0.9'%3E%3Cpath d='M383.973 215.615H216.028C204.43 215.615 195.035 225.034 195.035 236.663V362.951C195.035 374.58 204.43 383.999 216.028 383.999H383.973C395.572 383.999 404.966 374.58 404.966 362.951V236.663C404.966 225.034 395.572 215.615 383.973 215.615ZM279.008 257.711C284.802 257.711 289.504 262.426 289.504 268.235C289.504 274.045 284.802 278.759 279.008 278.759C273.214 278.759 268.511 274.045 268.511 268.235C268.511 262.426 273.214 257.711 279.008 257.711ZM373.477 352.427H226.525L263.252 305.08L289.504 336.747L326.242 289.294L373.477 352.427Z' fill='white' fill-opacity='0.7'/%3E%3C/g%3E%3C/svg%3E",
  ],
  url: "#",
};

const renderBlockContent = (attributes: any): React.JSX.Element => {
  let payload = {
    account_id: attributes.accountId,
    element: `rec-container-${attributes.blockId}`,
    block_id: attributes.blockId,
    recommendation_type: attributes.recommendationKind,
    content_collection_id: attributes.collection,
    interest_engine_id: attributes.interestEngine,
    segment_id: "",
    url: "",
    number_of_recommendations: attributes.numberOfRecommendations,
    dont_shuffle_results: attributes.doNotShuffle ? 1 : 0,
    include_viewed_content: attributes.includeRecentlyViewedContent ? 1 : 0,
    show_headline: attributes.showHeadline ? 1 : 0,
    show_image: attributes.showImage ? 1 : 0,
    show_body: attributes.showBody ? 1 : 0,
    preview_uid: attributes.previewUID,
  };

  const encodedPayload = btoa(JSON.stringify(payload));

  const element = (
    <div
      id={`rec-container-${attributes.block_id}`}
      data-rec-config={encodedPayload}
    >
      <div className="rec-wrapper">
        <div className="loading">Loading save...</div>
      </div>
    </div>
  );

  return element;
};

export default function save(props) {
  var attributes = props.attributes;

  return renderBlockContent(attributes);
}
