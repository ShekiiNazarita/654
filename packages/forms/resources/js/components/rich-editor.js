import Trix from 'trix/dist/trix'

import 'trix/dist/trix.css'
import '../../css/components/rich-editor.css'

Trix.config.blockAttributes.default.tagName = 'p'

Trix.config.blockAttributes.default.breakOnReturn = true

Trix.config.blockAttributes.heading = {
    tagName: 'h2',
    terminal: true,
    breakOnReturn: true,
    group: false
}

Trix.config.blockAttributes.subHeading = {
    tagName: 'h3',
    terminal: true,
    breakOnReturn: true,
    group: false
}

Trix.Block.prototype.breaksOnReturn = function () {
    const lastAttribute = this.getLastAttribute()
    const blockConfig = Trix.getBlockConfig(lastAttribute ? lastAttribute : 'default')

    return blockConfig?.breakOnReturn ?? false
}

Trix.LineBreakInsertion.prototype.shouldInsertBlockBreak = function () {
    if (this.block.hasAttributes() && this.block.isListItem() && (! this.block.isEmpty())) {
        return this.startLocation.offset > 0
    } else {
        return (! this.shouldBreakFormattedBlock()) ? this.breaksOnReturn : false
    }
}

export default (Alpine) => {
    Alpine.data('richEditorFormComponent', ({
        shouldDisableFileAttachmentCaptions,
        state,
    }) => {
        return {
            state,

            init: function () {
                if (shouldDisableFileAttachmentCaptions) {
                    Trix.config.attachments.preview.caption = { name: false, size: false }
                }

                this.$refs.trix?.editor?.loadHTML(this.state)

                this.$watch('state', () => {
                    if (document.activeElement === this.$refs.trix) {
                        return
                    }

                    this.$refs.trix?.editor?.loadHTML(this.state)
                })
            }
        }
    })
}
