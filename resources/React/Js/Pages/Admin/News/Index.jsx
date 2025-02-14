import React from 'react';
// import { Link, usePage } from '@inertiajs/react';
import { Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
  Plus, Edit2, Trash2, Search, Filter, Eye, 
  Calendar, Tag, ArrowUp, ArrowDown, AlertCircle, 
  Star, Clock, FileImage, MessageSquare
} from 'lucide-react';

export default function Index({ news }) {
  const [searchTerm, setSearchTerm] = React.useState('');
  const [sortConfig, setSortConfig] = React.useState({ key: 'published_at', direction: 'desc' });
  const [selectedCategory, setSelectedCategory] = React.useState('all');
  const [showDeleteModal, setShowDeleteModal] = React.useState(false);
  const [itemToDelete, setItemToDelete] = React.useState(null);

  const categories = ['all', ...new Set(news.data.map(item => item.category))];

  const sortData = (key) => {
    let direction = 'asc';
    if (sortConfig.key === key && sortConfig.direction === 'asc') {
      direction = 'desc';
    }
    setSortConfig({ key, direction });
  };

  const filteredAndSortedNews = React.useMemo(() => {
    let filtered = news.data.filter(item => 
      item.title.toLowerCase().includes(searchTerm.toLowerCase()) &&
      (selectedCategory === 'all' || item.category === selectedCategory)
    );

    if (sortConfig.key) {
      filtered.sort((a, b) => {
        if (a[sortConfig.key] < b[sortConfig.key]) {
          return sortConfig.direction === 'asc' ? -1 : 1;
        }
        if (a[sortConfig.key] > b[sortConfig.key]) {
          return sortConfig.direction === 'asc' ? 1 : -1;
        }
        return 0;
      });
    }

    return filtered;
  }, [news.data, searchTerm, selectedCategory, sortConfig]);

  const handleDelete = (item) => {
    setItemToDelete(item);
    setShowDeleteModal(true);
  };

  const confirmDelete = () => {
    if (itemToDelete) {
      router.delete(route('admin.news.destroy', itemToDelete.id));
    }
    setShowDeleteModal(false);
    setItemToDelete(null);
  };

  return (
    <AuthenticatedLayout>
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* En-tête avec statistiques */}
        <div className="mb-8">
          <div className="flex justify-between items-center mb-6">
            <h1 className="text-3xl font-bold text-gray-900">Actualités</h1>
            <Link
              href={route('admin.news.create')}
              className="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors shadow-sm"
            >
              <Plus className="w-5 h-5 mr-2" />
              Nouvelle actualité
            </Link>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            {[
              { label: 'Total', value: news.data.length, icon: Tag },
              { label: 'À la une', value: news.data.filter(n => n.featured).length, icon: Star },
              { label: 'Vues totales', value: news.data.reduce((acc, n) => acc + n.views, 0), icon: Eye },
              { label: 'Ce mois', value: news.data.filter(n => new Date(n.published_at).getMonth() === new Date().getMonth()).length, icon: Calendar }
            ].map((stat, idx) => (
              <div key={idx} className="bg-white rounded-xl p-6 shadow-sm">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-gray-500">{stat.label}</p>
                    <p className="text-2xl font-semibold mt-1">{stat.value}</p>
                  </div>
                  <stat.icon className="w-8 h-8 text-primary opacity-80" />
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Filtres et recherche */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="text"
                placeholder="Rechercher par titre..."
                className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
              />
            </div>
            <div className="relative">
              <Filter className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <select
                className="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary appearance-none"
                value={selectedCategory}
                onChange={(e) => setSelectedCategory(e.target.value)}
              >
                {categories.map(category => (
                  <option key={category} value={category}>
                    {category === 'all' ? 'Toutes les catégories' : category}
                  </option>
                ))}
              </select>
            </div>
          </div>
        </div>

        {/* Liste des actualités */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredAndSortedNews.map((item) => (
            <div key={item.id} className="bg-white rounded-xl shadow-sm overflow-hidden group">
              <div className="relative">
                {item.image ?(
                    <img
                    src={'/storage/'+item.image}
                    alt={item.title}
                    className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                  />
                ):
                (
                <FileImage className="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300 bg-gray-300" />

                )}
                
                {item.featured && (
                  <div className="absolute top-2 right-2 bg-yellow-400 text-white px-2 py-1 rounded-md text-xs font-medium flex items-center">
                    <Star className="w-3 h-3 mr-1" />
                    À la une
                  </div>
                )}
              </div>
              
              <div className="p-6">
                <div className="flex items-center space-x-2 mb-3">
                  <span className="px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                    {item.category}
                  </span>
                  <span className="text-gray-500 text-sm flex items-center">
                    <Clock className="w-4 h-4 mr-1" />
                    {item.read_time} min
                  </span>
                </div>
                
                <h3 className="text-lg font-semibold mb-2 line-clamp-2">{item.title}</h3>
                <p className="text-gray-600 text-sm mb-4 line-clamp-2">{item.excerpt}</p>
                
                <div className="flex items-center justify-between pt-4 border-t">
                  <div className="flex items-center space-x-4 text-sm text-gray-500">
                    <span className="flex items-center">
                      <Eye className="w-4 h-4 mr-1" />
                      {item.views}
                    </span>
                    <span className="flex items-center">
                      <MessageSquare className="w-4 h-4 mr-1" />
                      {item.comments_count || 0}
                    </span>
                  </div>
                  
                  <div className="flex items-center space-x-2">
                    {/* <Link
                      href={route('admin.news.show', item.id)}
                      className="p-1 text-gray-400 hover:text-primary transition-colors"
                    >
                      <Eye className="w-5 h-5" />
                    </Link> */}
                    <Link
                      href={route('admin.news.edit', item.id)}
                      className="p-1 text-gray-400 hover:text-yellow-600 transition-colors"
                    >
                      <Edit2 className="w-5 h-5" />
                    </Link>
                    <button
                      onClick={() => handleDelete(item)}
                      className="p-1 text-gray-400 hover:text-red-600 transition-colors"
                    >
                      <Trash2 className="w-5 h-5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>

        {filteredAndSortedNews.length === 0 && (
          <div className="text-center py-6 bg-white rounded-xl shadow-sm">
            <AlertCircle className="mx-auto h-12 w-12 text-gray-400" />
            <h3 className="mt-2 text-sm font-medium text-gray-900">Aucune actualité trouvée</h3>
            <p className="mt-1 text-sm text-gray-500">
              Commencez par créer une nouvelle actualité ou modifiez vos filtres.
            </p>
          </div>
        )}
      </div>

      {/* Modal de confirmation de suppression */}
      {showDeleteModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 className="text-lg font-medium text-gray-900 mb-4">
              Confirmer la suppression
            </h3>
            <p className="text-gray-500 mb-6">
              Êtes-vous sûr de vouloir supprimer cette actualité ? Cette action est irréversible.
            </p>
            <div className="flex justify-end space-x-4">
              <button
                onClick={() => setShowDeleteModal(false)}
                className="px-4 py-2 text-gray-700 hover:text-gray-900"
              >
                Annuler
              </button>
              <button
                onClick={confirmDelete}
                className="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
              >
                Supprimer
              </button>
            </div>
          </div>
        </div>
      )}
    </AuthenticatedLayout>
  );
}
