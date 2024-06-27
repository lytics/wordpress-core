declare interface Window {
  jstag: any;
  _lytics_rec_render: any;
  wp: {
    blocks: any;
    element: any;
    blockEditor: any;
    components: any;
  };
}

declare const lyticsBlockData: {
  account_id: string;
  content_collections: { [key: string]: number };
  engines: { [key: string]: number };
};
