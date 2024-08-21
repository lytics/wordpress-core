import CodeMirror from "codemirror/lib/codemirror";
import "codemirror/mode/javascript/javascript";
import "codemirror/lib/codemirror.css";
import "codemirror/theme/monokai.css";

// render the codemirror editor on admin settings page
if (document.getElementById("jsonInput")) {
  CodeMirror.fromTextArea(document.getElementById("jsonInput"), {
    mode: "application/json",
    lineNumbers: true,
    autoCloseBrackets: true,
    matchBrackets: true,
    theme: "monokai",
  });
}
