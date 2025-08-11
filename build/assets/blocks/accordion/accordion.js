import CKEditor from 'ckeditor4-vue'
import draggable from 'vuedraggable'

Vue.use(CKEditor)

window.Concrete.Vue.createContext('accordion', {
    CKEditor,
    draggable
})
