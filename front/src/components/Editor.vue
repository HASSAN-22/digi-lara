<template>
  <div class="block ltr">
    <QuillEditor
        ref="editor"
        class="quill-editor"
        :placeholder="placeholder"
        :options="options"
        :v-model="modelValue"
        @ready="onEditorReady($event)"
        @update:content="$emit('update:modelValue', contentType === 'text' ? this.getTEXT() : this.getHTML())"
    />
  </div>
</template>

<script>
import {QuillEditor} from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import store from "@/store";

export default{
  name: "Editor",
  components: {QuillEditor},
  props: {
    modelValue: String,
    placeholder: {
      type: String,
      default: "نظر خود را بنویسید.."
    },
    contentType: {
      type: String,
      default: "text"
    }
  },
  emits: ['update:modelValue'],
  computed: {
    options() {
      return {
        theme: 'snow',
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],

            [{'header': 1}, {'header': 2}],               // custom button values
            [{'list': 'ordered'}, {'list': 'bullet'}],
            [{'script': 'sub'}, {'script': 'super'}],      // superscript/subscript
            [{'indent': '-1'}, {'indent': '+1'}],          // outdent/indent
            [{'direction': 'rtl'}],                         // text direction

            [{'size': ['small', false, 'large', 'huge']}],  // custom dropdown
            [{'header': [1, 2, 3, 4, 5, 6, false]}],

            [{'color': []}, {'background': []}],          // dropdown with defaults from theme
            [{'font': []}],
            [{'align': []}],

            ['clean']                                         // remove formatting button
          ]
        }
      }
    },
    editor() {
      return this.$refs.editor.getQuill()
    }
  },
  mounted() {
    setTimeout(()=>{
      this.editor.format('direction', 'rtl');
      this.setHTML(this.modelValue);
      store.state.showDescription = true;
    },700)

  },
  methods: {
    getHTML() {
      return this.$refs.editor.getHTML();
    },
    getTEXT() {
      return this.$refs.editor.getText();
    },
    setHTML(value) {
      return this.$refs.editor.setHTML(value);
    },
    onEditorReady(editor) {
      editor.getModule('toolbar').addHandler('image', () => this.imageHandler(editor));
    }
  },
}
</script>

<style>
.ql-editor {
  direction: rtl;
  text-align: right;
  font-size: initial;
}

.quill-editor img {
  max-width: 100%;
  height: auto;
}

.ql-toolbar.ql-snow .ql-formats {
  margin-right: 0;
  margin-left: 0;
  margin-inline-end: 15px;
}

.ql-snow .ql-tooltip {
  z-index: 99999;
}

.ql-snow .ql-toolbar .ql-formats {
  margin: 8px;
}

.ql-snow .ql-toolbar .ql-formats:first-child {
  margin-inline-start: 12px;
}

.ql-snow .ql-editor pre.ql-syntax {
  direction: ltr;
  text-align: left;
}
</style>