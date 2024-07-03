export interface Recommendation {
  url: string;
  title: string;
  imageurls: string[];
  description: string;
}

export interface RecommendationOptions {
  accountId?: string;
  element?: string;
  type: string;
  collection: string;
  engine: string;
  segment?: string;
  url?: string;
  noShuffle?: boolean;
  includeViewed?: boolean;
  maxItems?: number;
  showHeadline?: boolean;
  showImage?: boolean;
  showBody?: boolean;
  previewUID?: string;
}

export async function getRecommendation(
  options: RecommendationOptions
): Promise<Recommendation[]> {
  let baseURL = `https://api.lytics.io/api/content/recommend/${options.accountId}/user/_uid/${options.previewUID}`;

  let parts = [];
  // add account id
  if (!options.accountId) {
    console.error("Account ID is required to generate recommendations.");
    return [];
  }

  parts.push(`account=${options.accountId}`);

  // check collection
  if (options.collection) {
    parts.push(`contentsegment=${options.collection}`);
  }

  // check engine
  if (options.engine) {
    parts.push(`config=${options.engine}`);
  }

  // check shuffle
  if (options.noShuffle) {
    parts.push("shuffle=false");
  } else {
    parts.push("shuffle=true");
  }

  // check excludeViewed
  if (options.includeViewed) {
    parts.push("visited=true");
  } else {
    parts.push("visited=false");
  }

  // set limit
  parts.push("limit=15");

  // build url from base and parts
  const url = `${baseURL}?${parts.join("&")}`;
  const response = await fetch(url);
  const data = await response.json();

  return data.data;
}
