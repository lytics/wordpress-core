import React from "react";
import "./editor.scss";

const renderBlockContent = (attributes: any): React.JSX.Element => {
  let payload = {
    account_id: attributes.accountId,
    element: `lytics-rec-container-${attributes.blockId}`,
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
      id={`lytics-rec-container-${attributes.block_id}`}
      data-rec-config={encodedPayload}
      data-rec-env="prod"
    >
      <div className="lytics-rec-wrapper">
        <div className="lytics-rec-loading"></div>
      </div>
    </div>
  );

  return element;
};

export default function save(props) {
  var attributes = props.attributes;

  return renderBlockContent(attributes);
}
