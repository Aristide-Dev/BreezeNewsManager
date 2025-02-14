import React from 'react';

export default function ContentRenderer({ content }) {
  const renderTextContent = (child) => {
    if (!child) return null;
    
    let text = child.text || '';
    
    // Application du formatage
    if (child.format & 1) text = <strong>{text}</strong>;
    if (child.format & 2) text = <em>{text}</em>;
    if (child.format & 4) text = <u>{text}</u>;
    if (child.format & 8) text = <del>{text}</del>;
    if (child.format & 16) text = <code>{text}</code>;
    
    return text;
  };

  const renderNodeChildren = (children) => {
    if (!Array.isArray(children)) return null;
    
    return children.map((child, index) => {
      if (!child) return null;

      if (child.type === 'text') {
        return <React.Fragment key={index}>{renderTextContent(child)}</React.Fragment>;
      }

      if (child.type === 'link' && child.children) {
        return (
          <a
            key={index}
            href={child.url}
            className="text-primary hover:underline"
            target="_blank"
            rel="noopener noreferrer"
          >
            {child.children.map((linkChild, linkIndex) => (
              <React.Fragment key={linkIndex}>
                {renderTextContent(linkChild)}
              </React.Fragment>
            ))}
          </a>
        );
      }

      return null;
    });
  };

  const renderContent = () => {
    try {
      if (!content) return null;
      
      // Parse le JSON si c'est une chaîne
      const parsedContent = typeof content === 'string' ? JSON.parse(content) : content;
      
      // Vérifie si c'est bien un état Lexical
      if (!parsedContent?.root?.children) {
        return <p className="mb-4">{content}</p>;
      }

      return parsedContent.root.children.map((node, index) => {
        if (!node) return null;

        // Gestion des paragraphes
        if (node.type === 'paragraph') {
          return (
            <p key={index} className="mb-4">
              {renderNodeChildren(node.children)}
            </p>
          );
        }

        // Gestion des titres
        if (node.type === 'heading' && node.tag) {
          const HeadingTag = `h${node.tag}`;
          const headingClasses = {
            h1: 'text-3xl font-bold mb-4',
            h2: 'text-2xl font-bold mb-3',
            h3: 'text-xl font-bold mb-2',
          }[`h${node.tag}`] || '';

          return (
            <HeadingTag key={index} className={headingClasses}>
              {renderNodeChildren(node.children)}
            </HeadingTag>
          );
        }

        // Gestion des listes
        if (node.type === 'list') {
          const ListTag = node.listType === 'number' ? 'ol' : 'ul';
          const listClasses = node.listType === 'number' ? 'list-decimal' : 'list-disc';

          return (
            <ListTag key={index} className={`${listClasses} ml-6 mb-4`}>
              {node.children?.map((item, itemIndex) => (
                <li key={itemIndex}>
                  {renderNodeChildren(item.children)}
                </li>
              ))}
            </ListTag>
          );
        }

        // Gestion des citations
        if (node.type === 'quote') {
          return (
            <blockquote key={index} className="border-l-4 border-gray-200 pl-4 mb-4 italic">
              {renderNodeChildren(node.children)}
            </blockquote>
          );
        }

        // Gestion des blocs de code
        if (node.type === 'code') {
          return (
            <pre key={index} className="bg-gray-100 p-4 rounded-lg mb-4 overflow-x-auto">
              <code>{renderNodeChildren(node.children)}</code>
            </pre>
          );
        }

        return null;
      });
    } catch (error) {
      console.error('Erreur de parsing du contenu:', error);
      return <p className="mb-4">{content}</p>;
    }
  };

  return (
    <div className="prose max-w-none">
      {renderContent()}
    </div>
  );
}