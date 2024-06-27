import { blockConfig } from "./config";
import edit from "./edit";
import save from "./save";

const blocks = window.wp.blocks;
const element = window.wp.element;
const el = element.createElement;
const registerBlockType = blocks.registerBlockType;

// Create a new Lytics block category
const { getCategories, setCategories } = blocks;
const categories = getCategories().filter(
  (category) => category.slug !== "lytics"
);
setCategories([...categories, { slug: "lytics", title: "Lytics" }]);

// Register the Lytics recommendation block type
registerBlockType("lytics/recommendations", {
  ...blockConfig,
  edit: edit,
  save: save,
});
