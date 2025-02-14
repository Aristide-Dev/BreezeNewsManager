import React, { useState } from 'react';
import { useForm, Link } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Editor from '@/Components/LexicalEditor/Editor';
import { 
  FileImage, Tags, Calendar, Newspaper, 
  AlignLeft, BookOpen, Save, ArrowLeft, X
} from 'lucide-react';

export default function Create() {
  const { data, setData, post, errors, processing } = useForm({
    title: '',
    excerpt: '',
    content: '',
    image: null,
    category: '',
    tags: [],
    featured: false,
    published_at: '',
  });

  const [imagePreview, setImagePreview] = useState(null);

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    setData('image', file);
    
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setImagePreview(reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  const handleEditorChange = (newContent) => {
    const content = JSON.stringify(newContent);
    setData('content', content);
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    post(route('admin.news.store'));
  };

  return (
    <AuthenticatedLayout>
      <div className="max-w-5xl mx-auto px-4 py-8">
        {/* En-tête */}
        <div className="flex items-center justify-between mb-8">
          <div>
            <Link 
              href={route('admin.news.index')} 
              className="flex items-center text-gray-600 hover:text-primary mb-2"
            >
              <ArrowLeft className="w-4 h-4 mr-2" />
              Retour aux actualités
            </Link>
            <h1 className="text-3xl font-bold text-gray-900">Créer une actualité</h1>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Colonne principale */}
            <div className="lg:col-span-2 space-y-6">
              {/* Titre */}
              <div className="bg-white rounded-xl shadow-sm p-6">
                <div className="flex items-center mb-4">
                  <Newspaper className="w-5 h-5 text-gray-400 mr-2" />
                  <label className="text-lg font-medium text-gray-900">Titre</label>
                </div>
                <input
                  type="text"
                  value={data.title}
                  onChange={(e) => setData('title', e.target.value)}
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                  placeholder="Entrez le titre de l'actualité"
                />
                <InputError message={errors.title} className="mt-2" />
              </div>

              {/* Résumé */}
              <div className="bg-white rounded-xl shadow-sm p-6">
                <div className="flex items-center mb-4">
                  <AlignLeft className="w-5 h-5 text-gray-400 mr-2" />
                  <label className="text-lg font-medium text-gray-900">Résumé</label>
                </div>
                <textarea
                  value={data.excerpt}
                  onChange={(e) => setData('excerpt', e.target.value)}
                  rows="3"
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                  placeholder="Un bref résumé de l'actualité"
                ></textarea>
                <InputError message={errors.excerpt} className="mt-2" />
              </div>

              {/* Contenu */}
              <div className="bg-white rounded-xl shadow-sm p-6">
                <div className="flex items-center mb-4">
                  <BookOpen className="w-5 h-5 text-gray-400 mr-2" />
                  <label className="text-lg font-medium text-gray-900">Contenu</label>
                </div>
                <Editor
                  onChange={handleEditorChange}
                  initialContent={data.content}
                />
                <InputError message={errors.content} className="mt-2" />
              </div>
            </div>

            {/* Colonne latérale */}
            <div className="space-y-6">
              {/* Image */}
              <div className="bg-white rounded-xl shadow-sm p-6">
                <div className="flex items-center mb-4">
                  <FileImage className="w-5 h-5 text-gray-400 mr-2" />
                  <label className="text-lg font-medium text-gray-900">Image</label>
                </div>
                <div className="mt-2">
                  {imagePreview ? (
                    <div className="relative rounded-lg overflow-hidden mb-4">
                      <img
                        src={imagePreview}
                        alt="Preview"
                        className="w-full h-48 object-cover"
                      />
                      <button
                        type="button"
                        onClick={() => {
                          setImagePreview(null);
                          setData('image', null);
                        }}
                        className="absolute top-2 right-2 p-2 bg-red-300 rounded-full shadow-lg hover:bg-red-500 text-white"
                      >
                        <span className="sr-only">Supprimer</span>
                        
                        <X className="w-4 h-4 text-white" />
                      </button>
                    </div>
                  ) : (
                    <div className="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                      <div className="space-y-1 text-center">
                        <FileImage className="mx-auto h-12 w-12 text-gray-400" />
                        <div className="text-sm text-gray-600">
                          <label className="relative cursor-pointer rounded-md font-medium text-primary hover:text-primary-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-primary">
                            <span>Télécharger une image</span>
                            <input
                              type="file"
                              onChange={handleImageChange}
                              className="sr-only"
                              accept="image/*"
                            />
                          </label>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
                <InputError message={errors.image} className="mt-2" />
              </div>
              

              {/* Catégorie et Tags */}
              <div className="bg-white rounded-xl shadow-sm p-6">
                <div className="space-y-4">
                  <div>
                    <div className="flex items-center mb-2">
                      <Tags className="w-5 h-5 text-gray-400 mr-2" />
                      <label className="text-lg font-medium text-gray-900">Catégorie</label>
                    </div>
                    <input
                      type="text"
                      value={data.category}
                      onChange={(e) => setData('category', e.target.value)}
                      className="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                      placeholder="Catégorie"
                    />
                    <InputError message={errors.category} className="mt-2" />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700">Tags</label>
                    <input
                      type="text"
                      value={data.tags.join(', ')}
                      onChange={(e) => setData('tags', e.target.value.split(',').map(tag => tag.trim()))}
                      className="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                      placeholder="Séparez les tags par des virgules"
                    />
                    <InputError message={errors.tags} className="mt-2" />
                  </div>
                </div>
              </div>

              <div className="bg-white rounded-xl shadow-sm p-6">
                <div className="flex items-center">
                  <input
                    type="checkbox"
                    id="featured"
                    checked={data.featured}
                    onChange={(e) => setData('featured', e.target.checked)}
                    className="mr-3 w-5 h-5 text-primary focus:ring-2 focus:ring-primary border-gray-300 rounded"
                  />
                  <label htmlFor="featured" className="text-lg font-medium text-gray-900">Mettre à la une</label>
                </div>
                <InputError message={errors.featured} className="mt-2" />
              </div>


              {/* Date de publication */}
              <div className="bg-white rounded-xl shadow-sm p-6">
                <div className="flex items-center mb-4">
                  <Calendar className="w-5 h-5 text-gray-400 mr-2" />
                  <label className="text-lg font-medium text-gray-900">Publication</label>
                </div>
                <input
                  type="date"
                  value={data.published_at}
                  onChange={(e) => setData('published_at', e.target.value)}
                  className="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                />
              </div>
              <InputError message={errors.published_at} className="mt-2" />
            </div>
          </div>

          {/* Boutons d'action */}
          <div className="flex justify-end space-x-4">
            <Link
              href={route('admin.news.index')}
              className="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Annuler
            </Link>
            <button
              type="submit"
              disabled={processing}
              className="flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50"
            >
              <Save className="w-5 h-5 mr-2" />
              {processing ? 'Création...' : 'Créer l\'actualité'}
            </button>
          </div>
        </form>
      </div>
    </AuthenticatedLayout>
  );
}
