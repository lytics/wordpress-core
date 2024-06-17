(function (blocks, element, editor, components) {
  var el = element.createElement;
  var registerBlockType = blocks.registerBlockType;
  var InspectorControls = editor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;

  // Add a new category if it doesn't exist
  const { getCategories, setCategories } = blocks;

  const categories = getCategories().filter(
    (category) => category.slug !== "lytics"
  );

  setCategories([...categories, { slug: "lytics", title: "Lytics" }]);

  // register recommendation icon
  var recommendationIcon = el(
    "svg",
    { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 96 88" },
    el("path", {
      d: "M62 16L52 40L28 50L52 60L62 84L72 60L96 50L72 40L62 16ZM16.5 27.5L22 44L27.5 27.5L44 22L27.5 16.5L22 0L16.5 16.5L0 22L16.5 27.5ZM25.5 70.5L22 60L18.5 70.5L8 74L18.5 77.5L22 88L25.5 77.5L36 74L25.5 70.5Z",
      fill: "black",
    })
  );

  var renderBlockContent = function (attributes) {
    let items = [];

    let recommendations = [
      {
        image: "/wp-content/uploads/2021/11/product-thumb-18_optimized.webp",
        title: "Azure Haven Pet Retreat",
      },
      {
        image: "/wp-content/uploads/2021/11/product-thumb-12_optimized.webp",
        title: "Integer Malesuada",
      },
      {
        image:
          "/wp-content/uploads/2021/11/raoul-droog-Ea8rP2Ebp_4-unsplash_optimized.webp",
        title: "Elegant Paws Leather Cat Carrier",
      },
      {
        image: "/wp-content/uploads/2021/11/product-thumb-16_optimized.webp",
        title: "Serene Dream Pup Bed",
      },
    ];

    for (let i = 0; i < attributes.numberOfRecommendations; i++) {
      items.push(
        el(
          "div",
          {
            style: {
              display: "flex",
              flexDirection: "column",
              justifyContent: "flex-start",
              alignItems: "stretch",
              flex: "1",
              gap: "small",
              marginRight: "25px",
            },
            className: "rec-item",
          },
          [
            el("img", {
              style: { width: "100%" },
              className: "rec-img",
              alt: recommendations[i].title,
              src: recommendations[i].image,
            }),
            el(
              "div",
              { style: { marginTop: "10px" }, className: "rec-title" },
              el(
                "a",
                {
                  href: "#", // Placeholder link
                },
                el("strong", null, recommendations[i].title)
              )
            ),
          ]
        )
      );
    }

    return el(
      "div",
      {
        style: {},
      },
      [
        // el(
        //   "h3",
        //   {
        //     style: {
        //       textAlign: "center",
        //       marginBottom: "10px",
        //       marginTop: "50px",
        //     },
        //   },
        //   "Recommended Content"
        // ),
        el(
          "div",
          {
            style: {
              display: "flex",
              justifyContent: "space-between",
              alignItems: "stretch",
              flexWrap: "wrap",
              gap: "medium",
              marginTop: "25px",
              marginBottom: "50px",
            },
            id: "rec-container-811",
            "data-rec-config":
              "eyJhY2NvdW50X2lkIjoiMzI2OTUxZjc1MTQwMDE0YjgzYTkyMjM1ZTY5OTcyNTMiLCJlbGVtZW50IjoicmVjLWNvbnRhaW5lci04MTEiLCJibG9ja19pZCI6ODExLCJyZWNvbW1lbmRhdGlvbl90eXBlIjoiaW5kaXZpZHVhbCIsImNvbnRlbnRfY29sbGVjdGlvbl9pZCI6ImVuZ2xpc2hfY29udGVudF93aXRoX2ltYWdlcyIsImludGVyZXN0X2VuZ2luZV9pZCI6ImVlMjc1OTg5MWM0YzcwOTUzODNmZDI2ZjU4ODA5YTg5Iiwic2VnbWVudF9pZCI6IiIsInVybCI6IiIsIm51bWJlcl9vZl9yZWNvbW1lbmRhdGlvbnMiOiI0IiwiZG9udF9zaHVmZmxlX3Jlc3VsdHMiOjAsImluY2x1ZGVfdmlld2VkX2NvbnRlbnQiOjF9",
          },
          items
        ),
      ]
    );
  };

  registerBlockType("lytics/recommendations", {
    title: "Recommend Content",
    icon: recommendationIcon,
    category: "lytics",
    attributes: {
      recommendationKind: {
        type: "string",
        default: "individual",
      },
      interestEngine: {
        type: "string",
        default: "ee2759891c4c7095383fd26f58809a89",
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
    },
    edit: function (props) {
      var attributes = props.attributes;

      function updateRecommendationKind(value) {
        props.setAttributes({ recommendationKind: value });
      }

      function updateInterestEngine(value) {
        props.setAttributes({ interestEngine: value });
      }

      function updateCollection(value) {
        props.setAttributes({ collection: value });
      }

      function updateNumberOfRecommendations(value) {
        props.setAttributes({ numberOfRecommendations: parseInt(value) });
      }

      function updateDoNotShuffle(value) {
        props.setAttributes({ doNotShuffle: value });
      }

      function updateIncludeRecentlyViewedContent(value) {
        props.setAttributes({ includeRecentlyViewedContent: value });
      }

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
              options: [
                { label: "Select an Interest Engine", value: "" },
                {
                  label: "Default (Recommended)",
                  value: "ee2759891c4c7095383fd26f58809a89",
                },
              ],
              onChange: updateInterestEngine,
            }),
            el(SelectControl, {
              label: "Select a Content Collection",
              value: attributes.collection,
              options: [
                { label: "Select a Content Collection", value: "" },
                { label: "All Documents", value: "all_documents" },
                { label: "Content With Images", value: "content_with_images" },
                {
                  label: "Default Recommendation Collection",
                  value: "default_recommendations",
                },
                {
                  label: "English Content With Images",
                  value: "english_content_with_images",
                },
              ],
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
            })
          )
        ),
        renderBlockContent(attributes),
      ];
    },
    save: function (props) {
      var attributes = props.attributes;

      return renderBlockContent(attributes);
    },
  });
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.components
);
