// src/pages/News/Show.jsx
import React from 'react';
import { Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
  Calendar, Clock, Tag, Share2, ArrowLeft,
  Eye, Edit2, Trash2, Star, Globe
} from 'lucide-react';

export default function Show({ news }) {
  return (
    <AuthenticatedLayout>
      <div className="max-w-5xl mx-auto px-4 py-8">
        {/* En-tête */}
        <div className="mb-8">
          <Link
            href={route('admin.news.index')}
            className="inline-flex items-center text-gray-600 hover:text-primary"
          >
            <ArrowLeft className="w-4 h-4 mr-2" />
            Retour aux actualités
          </Link>
        </div>

        {/* Article principal */}
        <div className="bg-white rounded-xl shadow-lg overflow-hidden">
          {/* Image de couverture */}
          <div className="relative h-96">
            <img
              src={news.image}
              alt={news.title}
              className="w-full h-full object-cover"
            />
            {news.featured && (
              <div className="absolute top-4 right-4">
                <span className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                  <Star className="w-4 h-4 mr-2" />
                  À la une
                </span>
              </div>
            )}
          </div>

          {/* Contenu */}
          <div className="p-8">
            {/* Métadonnées */}
            <div className="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-500">
              <span className="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 text-primary font-medium">
                {news.category}
              </span>
              <span className="flex items-center">
                <Calendar className="w-4 h-4 mr-2" />
                {news.published_at}
              </span>
              <span className="flex items-center">
                <Clock className="w-4 h-4 mr-2" />
                {news.read_time} min de lecture
              </span>
              <span className="flex items-center">
                <Eye className="w-4 h-4 mr-2" />
                {news.views} vues
              </span>
              <span className="flex items-center">
                <Globe className="w-4 h-4 mr-2" />
                {news.status === 'published' ? 'Publié' : 'Brouillon'}
              </span>
            </div>

            {/* Titre */}
            <h1 className="text-4xl font-bold text-gray-900 mb-6">
              {news.title}
            </h1>

            {/* Résumé */}
            <div className="bg-gray-50 rounded-lg p-6 mb-8">
              <h2 className="text-lg font-semibold text-gray-900 mb-3">Résumé</h2>
              <p className="text-gray-600 leading-relaxed">
                {news.excerpt}
              </p>
            </div>

            {/* Contenu principal */}
            <div className="prose max-w-none mb-8">
              <div dangerouslySetInnerHTML={{ __html: news.content }} />
            </div>

            {/* Tags */}
            <div className="border-t border-gray-200 pt-8">
              <h2 className="text-lg font-semibold text-gray-900 mb-4">Tags</h2>
              <div className="flex flex-wrap gap-2">
                {news.tags.map((tag, index) => (
                  <span
                    key={index}
                    className="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-sm"
                  >
                    <Tag className="w-4 h-4 mr-2" />
                    {tag}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* Actions */}
        <div className="mt-8 flex justify-end space-x-4">
          <Link
            href={route('admin.news.edit', news.id)}
            className="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
          >
            <Edit2 className="w-4 h-4 mr-2" />
            Modifier
          </Link>
          <Link
            href={route('admin.news.destroy', news.id)}
            method="delete"
            as="button"
            className="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
          >
            <Trash2 className="w-4 h-4 mr-2" />
            Supprimer
          </Link>
          <button
            onClick={() => {
              // Logique de partage
              if (navigator.share) {
                navigator.share({
                  title: news.title,
                  text: news.excerpt,
                  url: window.location.href
                });
              }
            }}
            className="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
          >
            <Share2 className="w-4 h-4 mr-2" />
            Partager
          </button>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
