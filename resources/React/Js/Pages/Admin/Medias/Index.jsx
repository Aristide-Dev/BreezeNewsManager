import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useForm, Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
  Search, Filter, Grid, List, Image, Video, 
  Download, Share2, Trash2, Edit, Plus, ChevronLeft, ChevronRight, X, ImageIcon, VideoIcon
} from 'lucide-react';

export default function MediaIndex({ categories = [] }) {

  const { delete:destroy } = useForm({
    id: null,
  });
  const [deleteModalVisible, setDeleteModalVisible] = useState(false);
  const [selectedMedia, setSelectedMedia] = useState(null);
  const [medias, setMedias] = useState([]);
  const [loading, setLoading] = useState(true);
  const [view, setView] = useState('grid');
  const [filters, setFilters] = useState({
    search: '',
    type: 'all',
    category: 'all',
  });
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 12;

  const fetchMedia = async () => {
    setLoading(true);
    try {
      const response = await axios.get('/api/medias', {
        params: {
          type: filters.type !== 'all' ? filters.type : undefined,
          category: filters.category !== 'all' ? filters.category : undefined,
          search: filters.search || undefined,
        },
      });
      setMedias(response.data);
    } catch (error) {
      console.error('Erreur lors du chargement des médias:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchMedia();
  }, [filters]);

  const paginatedMedia = medias.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );

  const totalPages = Math.ceil(medias.length / itemsPerPage);

  const handleDelete = async (id) => {
    setSelectedMedia(id);
    setDeleteModalVisible(true);
  };

  const confirmDelete = async () => {
    try {
      await destroy(`/admin-panel/medias/${selectedMedia}`);
      fetchMedia();
      setDeleteModalVisible(false);
    } catch (error) {
      console.error('Erreur lors de la suppression:', error);
    }
  };

  const handleShare = (media) => {
    const baseUrl = window.location.origin;
    const fullUrl = isExternalUrl(media.url) 
      ? media.url 
      : `${baseUrl}/storage/${media.url}`;
    
    navigator.clipboard.writeText(fullUrl);
    alert('URL copiée dans le presse-papier');
  };

  const isExternalUrl = (url) => url.startsWith('http://') || url.startsWith('https://');

  return (
    <AuthenticatedLayout>
      <Head title="Médiathèque" />

      {/* Ajouter le modal de confirmation avant le contenu principal */}
      {deleteModalVisible && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <h3 className="text-lg font-semibold mb-4">Confirmer la suppression</h3>
            <p className="text-gray-600 mb-6">Êtes-vous sûr de vouloir supprimer ce média ?</p>
            <div className="flex justify-end gap-3">
              <button
                onClick={() => setDeleteModalVisible(false)}
                className="px-4 py-2 rounded-lg border hover:bg-gray-50"
              >
                Annuler
              </button>
              <button
                onClick={confirmDelete}
                className="px-4 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600"
              >
                Supprimer
              </button>
            </div>
          </div>
        </div>
      )}

      <div className="max-w-7xl mx-auto px-4 py-8">
        {/* En-tête avec recherche et filtres */}
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold text-gray-900">Médiathèque</h1>
          <a
            href={route('admin.medias.create')}
            className="flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 transition"
          >
            <Plus className="w-5 h-5 mr-2" />
            Ajouter un média
          </a>
        </div>

        {/* Barre de filtres */}
        <div className="bg-white rounded-xl shadow-sm p-4 mb-8">
          <div className="flex flex-wrap gap-4 items-center">
            <div className="flex-1 min-w-[200px]">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
                <input
                  type="text"
                  placeholder="Rechercher..."
                  value={filters.search}
                  onChange={(e) => setFilters({ ...filters, search: e.target.value })}
                  className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                />
              </div>
            </div>

            <select
              value={filters.type}
              onChange={(e) => setFilters({ ...filters, type: e.target.value })}
              className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
              <option value="all">Tous les types</option>
              <option value="image">Images</option>
              <option value="video">Vidéos</option>
            </select>

            <select
              value={filters.category}
              onChange={(e) => setFilters({ ...filters, category: e.target.value })}
              className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            >
              <option value="all">Toutes les catégories</option>
              {categories.map((category) => (
                <option key={category} value={category}>
                  {category}
                </option>
              ))}
            </select>

            <div className="flex items-center gap-2 border-l pl-4">
              <button
                onClick={() => setView('grid')}
                className={`p-2 rounded-lg ${view === 'grid' ? 'bg-gray-100' : 'hover:bg-gray-50'}`}
              >
                <Grid className="w-5 h-5" />
              </button>
              <button
                onClick={() => setView('list')}
                className={`p-2 rounded-lg ${view === 'list' ? 'bg-gray-100' : 'hover:bg-gray-50'}`}
              >
                <List className="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>

        {/* Liste des médias */}
        {loading ? (
          <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
          <div className="flex flex-col items-center">
            <div className="w-16 h-16 border-4 border-t-transparent border-primary rounded-full animate-spin"></div>
            <p className="mt-4 text-lg font-semibold text-white animate-pulse">
              Chargement en cours...
            </p>
          </div>
        </div>
        ) : view === 'grid' ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {paginatedMedia.map((media) => (
              <div key={media.id} className="bg-white rounded-xl shadow-sm overflow-hidden group">
                <div className="relative aspect-video">
                  {media.type === 'image' ? (
                    <img
                      src={isExternalUrl(media.url) ? media.url : `/storage/${media.url}`}
                      alt={media.title}
                      className="w-full h-full object-cover"
                      onClick={() => setSelectedMedia(media)}
                    />
                  ) : (
                    <iframe
                      className="w-full h-full object-cover"
                      src={isExternalUrl(media.embed_url) ? media.embed_url : `/storage/${media.embed_url}`}
                      title={media.title}
                      frameBorder="0"
                      allowFullScreen
                    />
                  )}
                  <div className="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <div className="flex gap-2">
                      <button
                        onClick={() => handleShare(media)}
                        className="p-2 bg-white rounded-full hover:bg-gray-100"
                      >
                        <Share2 className="w-5 h-5" />
                      </button>
                      <a
                        href={isExternalUrl(media.url) ? media.url : `/storage/${media.url}`}
                        download
                        className="p-2 bg-white rounded-full hover:bg-gray-100"
                      >
                        <Download className="w-5 h-5" />
                      </a>
                      <a
                        href={route('admin.medias.edit', media)}
                        className="p-2 bg-white rounded-full hover:bg-gray-100"
                      >
                        <Edit className="w-5 h-5" />
                      </a>
                      <button
                        onClick={() => handleDelete(media.id)}
                        className="p-2 bg-white rounded-full hover:bg-gray-100"
                      >
                        <Trash2 className="w-5 h-5 text-red-500" />
                      </button>
                    </div>
                  </div>
                </div>
                <div className="p-4">
                  <h3 className="font-semibold text-gray-900 mb-1">{media.title}</h3>
                  <p className="text-sm text-gray-500">{media.category}</p>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <table className="min-w-full bg-white rounded-xl shadow-sm overflow-hidden">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Média
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Type
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Catégorie
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody className="bg-white divide-y divide-gray-200">
                {paginatedMedia.map((media) => (
                  <tr key={media.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="h-10 w-10 flex-shrink-0">
                          {media.type === 'image' ? (
                            <img
                              src={isExternalUrl(media.url) ? media.url : `/storage/${media.url}`}
                              alt={media.title}
                              className="h-10 w-10 rounded-lg object-cover"
                            />
                          ) : (
                            
                            <iframe 
                              className="h-10 w-10 rounded-lg object-cover" 
                              src={isExternalUrl(media.embed_url) ? media.embed_url : `/storage/${media.embed_url}`}
                              title="YouTube video player" 
                              frameborder="0" 
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                              referrerpolicy="strict-origin-when-cross-origin" 
                              allowfullscreen
                              onClick={() => setSelectedMedia(media)}
                            >
                          </iframe>
                            // <video
                            //   src={media.url}
                            //   className="h-10 w-10 rounded-lg object-cover"
                            // />
                          )}
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">{media.title}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                        {media.type === "video" ? (
                          <VideoIcon className='text-red-500'/>
                        ):(
                          
                          <ImageIcon className='text-blue-500'/>
                        )}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {media.category}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {new Date(media.created_at).toLocaleDateString()}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div className="flex justify-end gap-2">
                        <button
                          onClick={() => handleShare(media)}
                          className="text-gray-400 hover:text-gray-500"
                        >
                          <Share2 className="w-5 h-5" />
                        </button>
                        <a
                          href={media.url}
                          download
                          className="text-gray-400 hover:text-gray-500"
                        >
                          <Download className="w-5 h-5" />
                        </a>
                        <button
                          onClick={() => handleDelete(media.id)}
                          className="text-red-400 hover:text-red-500"
                        >
                          <Trash2 className="w-5 h-5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
          </table>
        )}

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="flex justify-center mt-8">
            <button
              onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
              disabled={currentPage === 1}
              className="p-2 rounded-lg border hover:bg-gray-50 disabled:opacity-50"
            >
              <ChevronLeft className="w-5 h-5" />
            </button>
            {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
              <button
                key={page}
                onClick={() => setCurrentPage(page)}
                className={`px-4 py-2 rounded-lg ${
                  currentPage === page ? 'bg-primary text-white' : 'hover:bg-gray-50'
                }`}
              >
                {page}
              </button>
            ))}
            <button
              onClick={() => setCurrentPage((p) => Math.min(totalPages, p + 1))}
              disabled={currentPage === totalPages}
              className="p-2 rounded-lg border hover:bg-gray-50 disabled:opacity-50"
            >
              <ChevronRight className="w-5 h-5" />
            </button>
          </div>
        )}
      </div>
    </AuthenticatedLayout>
  );
}
