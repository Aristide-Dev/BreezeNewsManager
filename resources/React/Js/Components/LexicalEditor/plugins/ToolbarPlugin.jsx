import { useLexicalComposerContext } from '@lexical/react/LexicalComposerContext';
import {
    Bold, Italic, Underline, List, Link, Code,
    AlignLeft, AlignCenter, AlignRight, Heading1, Heading2
} from 'lucide-react';

export default function ToolbarPlugin() {
    const [editor] = useLexicalComposerContext();

    const formatHeading = (level) => {
        editor.update(() => {
            const selection = $getSelection();
            if ($isRangeSelection(selection)) {
                $setBlocksType(selection, () => $createHeadingNode(`h${level}`));
            }
        });
    };

    return (
        <div className="editor-toolbar flex items-center gap-1 p-2 border-b border-gray-200">
            <button
                onClick={() => editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'bold')}
                className="p-2 hover:bg-gray-100 rounded"
                title="Gras"
            >
                <Bold className="w-4 h-4" />
            </button>
            <button
                onClick={() => editor.dispatchCommand(FORMAT_TEXT_COMMAND, 'italic')}
                className="p-2 hover:bg-gray-100 rounded"
                title="Italique"
            >
                <Italic className="w-4 h-4" />
            </button>
            {/* Ajoutez d'autres boutons de formatage ici */}
        </div>
    );
} 