(function (blocks, element, components) {
  var el = element.createElement;
  var SelectControl = components.SelectControl;
  var TextControl = components.TextControl;

  blocks.registerBlockType("vsai/template-block", {
    title: "VSAI Template",
    icon: "layout",
    category: "widgets",
    attributes: {
      name: {
        type: "string",
        default: "",
      },
      data: {
        type: "string",
        default: "{}",
      },
    },
    edit: function (props) {
      var name = props.attributes.name;
      var data = props.attributes.data;

      function onChangeName(newName) {
        props.setAttributes({ name: newName });
      }

      function onChangeData(newData) {
        props.setAttributes({ data: newData });
      }

      return el(
        "div",
        { className: props.className },
        el(SelectControl, {
          label: "Select Template",
          value: name,
          options: [
            { label: "Select a template", value: "" },
            { label: "Upload Form", value: "upload_form" },
            { label: "Main Page", value: "main_page" },
          ],
          onChange: onChangeName,
        }),
        el(TextControl, {
          label: "Template Data (JSON)",
          value: data,
          onChange: onChangeData,
        })
      );
    },
    save: function (props) {
      return null; // We'll render this dynamically in PHP
    },
  });
})(window.wp.blocks, window.wp.element, window.wp.components);
