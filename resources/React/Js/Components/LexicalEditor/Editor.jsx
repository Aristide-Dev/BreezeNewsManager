import React, { useCallback } from 'react';
import { LexicalComposer } from '@lexical/react/LexicalComposer';
import { ContentEditable } from '@lexical/react/LexicalContentEditable';
import { RichTextPlugin } from '@lexical/react/LexicalRichTextPlugin';
import { HistoryPlugin } from '@lexical/react/LexicalHistoryPlugin';
import { OnChangePlugin } from '@lexical/react/LexicalOnChangePlugin';
import { ListPlugin } from '@lexical/react/LexicalListPlugin';
import { LinkPlugin } from '@lexical/react/LexicalLinkPlugin';
import { MarkdownShortcutPlugin } from '@lexical/react/LexicalMarkdownShortcutPlugin';
import LexicalErrorBoundary from '@lexical/react/LexicalErrorBoundary';
import { $getRoot, $createParagraphNode, $createTextNode } from 'lexical';
import Toolbar from './Toolbar';

import { LinkNode } from '@lexical/link';
import { ListNode, ListItemNode } from '@lexical/list';
import { HorizontalRuleNode } from '@lexical/react/LexicalHorizontalRuleNode';
import { HeadingNode, QuoteNode } from '@lexical/rich-text';
import { CodeNode } from '@lexical/code';

const theme = {
  paragraph: 'mb-4',
  heading: {
    h1: 'text-3xl font-bold mb-4',
    h2: 'text-2xl font-bold mb-3',
  },
  list: {
    ul: 'list-disc ml-4 mb-4',
    ol: 'list-decimal ml-4 mb-4',
  },
  quote: 'border-l-4 border-gray-200 pl-4 mb-4 italic',
  code: 'bg-gray-100 rounded px-2 py-1 font-mono',
};

function Placeholder() {
  return (
    <div className="absolute top-[60px] left-3 text-gray-400 pointer-events-none">
      Commencez à écrire...
    </div>
  );
}

export default function Editor({ onChange, initialContent }) {
  const prepareInitialState = () => {
    try {
      // Essaie de parser le JSON si c'est déjà un état Lexical valide
      JSON.parse(initialContent);
      return initialContent;
    } catch {
      // Si ce n'est pas un JSON valide, on retourne null pour laisser Lexical créer un état par défaut
      return null;
    }
  };

  const initialConfig = {
    namespace: 'MyArticleEditor',
    theme,
    onError(error) {
      console.error(error);
    },
    nodes: [
      LinkNode,
      ListNode,
      ListItemNode,
      HorizontalRuleNode,
      HeadingNode,
      QuoteNode,
      CodeNode,
    ],
    editorState: prepareInitialState(),
  };

  // Fonction pour initialiser l'éditeur avec du contenu texte
  const initializeEditor = useCallback((editor) => {
    if (initialContent && !prepareInitialState()) {
      editor.update(() => {
        const root = $getRoot();
        const paragraph = $createParagraphNode();
        paragraph.append($createTextNode(initialContent));
        root.append(paragraph);
      });
    }
  }, [initialContent]);

  const handleChange = useCallback(
    (editorState) => {
      editorState.read(() => {
        // Pour l'instant, on conserve la conversion en texte simple
        const content = editorState.toJSON();
        onChange(JSON.stringify(content));
      });
    },
    [onChange]
  );

  return (
    <LexicalComposer initialConfig={initialConfig}>
      <div className="editor-container border border-gray-300 rounded-lg overflow-hidden">
        <Toolbar />
        <div className="relative min-h-[300px] p-3">
          <RichTextPlugin
            contentEditable={
              <ContentEditable className="outline-none min-h-[300px]" />
            }
            placeholder={<Placeholder />}
            ErrorBoundary={LexicalErrorBoundary}
          />
          <HistoryPlugin />
          <ListPlugin />
          <LinkPlugin />
          <MarkdownShortcutPlugin />
          <OnChangePlugin onChange={handleChange} />
        </div>
      </div>
    </LexicalComposer>
  );
} 