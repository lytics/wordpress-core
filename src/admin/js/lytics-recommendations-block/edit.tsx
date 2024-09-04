import React from "react";
import "./editor.scss";

// WordPress dependencies
const element = window.wp.element;
const editor = window.wp.blockEditor;
const components = window.wp.components;

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

  // Render the block
  if (typeof window._lytics_rec_render !== "undefined") {
    setTimeout(() => {
      window._lytics_rec_render.render();
    }, 1000);
  } else {
    console.warn("Lytics render not found.");
  }

  const element = (
    <div
      id={`lytics-rec-container-${attributes.block_id}`}
      data-rec-config={encodedPayload}
    >
      <div className="lytics-rec-wrapper">
        <div className="lytics-rec-loading"></div>
      </div>
    </div>
  );

  return element;
};

export default function edit(props) {
  const attributes = props.attributes;
  const el = element.createElement;
  const { InspectorControls } = editor;
  const { PanelBody, SelectControl, CheckboxControl, TextControl } = components;

  if (typeof lyticsBlockData !== "undefined") {
    props.setAttributes({
      accountId: lyticsBlockData.account_id,
      contentCollections: lyticsBlockData.content_collections,
      engines: lyticsBlockData.engines,
    });
  }

  const updateRecommendationKind = (value) => {
    props.setAttributes({ recommendationKind: value });
  };

  const updateInterestEngine = (value) => {
    props.setAttributes({ interestEngine: value });
  };

  const updateCollection = (value) => {
    props.setAttributes({ collection: value });
  };

  const updateNumberOfRecommendations = (value) => {
    props.setAttributes({ numberOfRecommendations: parseInt(value) });
  };

  const updateDoNotShuffle = (value) => {
    props.setAttributes({ doNotShuffle: value });
  };

  const updateIncludeRecentlyViewedContent = (value) => {
    props.setAttributes({ includeRecentlyViewedContent: value });
  };

  const updateShowHeadline = (value) => {
    props.setAttributes({ showHeadline: value });
  };

  const updateShowImage = (value) => {
    props.setAttributes({ showImage: value });
  };

  const updateShowBody = (value) => {
    props.setAttributes({ showBody: value });
  };

  const updatePreviewUID = (value) => {
    props.setAttributes({ previewUID: value });
  };

  return [
    el(
      InspectorControls,
      { key: "inspector" },
      el(
        PanelBody,
        { title: "Settings" },

        el(SelectControl, {
          label: "What kind of recommendation would you like to make?",
          value: attributes.recommendationKind,
          options: [
            { label: "Choose Recommendation Type", value: "" },
            {
              label: "Recommend content that is relevant to each visitor.",
              value: "individual",
            },
          ],
          onChange: updateRecommendationKind,
        }),
        el(SelectControl, {
          label: "Interest Engine to Power Recommendation",
          value: attributes.interestEngine,
          options: attributes.engines,
          onChange: updateInterestEngine,
        }),
        el(SelectControl, {
          label: "Select a Content Collection",
          value: attributes.collection,
          options: attributes.contentCollections,
          onChange: updateCollection,
        }),
        el(SelectControl, {
          label: "How many recommendations would you like to show?",
          value: attributes.numberOfRecommendations,
          options: [
            { label: "1", value: 1 },
            { label: "2", value: 2 },
            { label: "3", value: 3 },
            { label: "4", value: 4 },
            { label: "5", value: 5 },
          ],
          onChange: updateNumberOfRecommendations,
        }),
        el(SelectControl, {
          label: "Shuffle Recommendations",
          value: attributes.doNotShuffle,
          options: [
            { label: "Yes", value: false },
            { label: "No", value: true },
          ],
          onChange: updateDoNotShuffle,
        }),
        el(SelectControl, {
          label: "Include Recently Viewed Content",
          value: attributes.includeRecentlyViewedContent,
          options: [
            { label: "Yes", value: true },
            { label: "No", value: false },
          ],
          onChange: updateIncludeRecentlyViewedContent,
        }),
        el(CheckboxControl, {
          label: "Show Headline",
          checked: attributes.showHeadline,
          onChange: updateShowHeadline,
        }),
        el(CheckboxControl, {
          label: "Show Image",
          checked: attributes.showImage,
          onChange: updateShowImage,
        }),
        el(CheckboxControl, {
          label: "Show Body",
          checked: attributes.showBody,
          onChange: updateShowBody,
        }),
        el(TextControl, {
          label: "Preview UID (optional)",
          value: attributes.previewUID,
          onChange: updatePreviewUID,
        })
      )
    ),
    renderBlockContent(attributes),
  ];
}
