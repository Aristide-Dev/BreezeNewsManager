import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { FileIcon, defaultStyles  } from "react-file-icon";
import { 
  FileText, Search, Filter, Download, Trash2, Edit,
  Eye, Plus, Calendar, User, Tag, ChevronDown, X,
  SortAsc, SortDesc, FileArchive, FileImage
} from 'lucide-react';

export default function DocumentList({ documents, categories }) {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [sortBy, setSortBy] = useState('date');
  const [sortOrder, setSortOrder] = useState('desc');
  const [showFilters, setShowFilters] = useState(false);
  console.log('categories', categories);

  // const categories = [
  //   { id: 'all', label: 'Tous les documents' },
  //   { id: 'reports', label: 'Rapports' },
  //   { id: 'presentations', label: 'Présentations' },
  //   { id: 'forms', label: 'Formulaires' },
  //   { id: 'others', label: 'Autres' }
  // ];

  // const getFileIcon = (fileType) => {
  //   switch (fileType) {
  //     case 'pdf': return <FileText className="w-6 h-6 text-red-500" />;
  //     case 'image': return <FileImage className="w-6 h-6 text-blue-500" />;
  //     case 'archive': return <FileArchive className="w-6 h-6 text-yellow-500" />;
  //     default: return <FileText className="w-6 h-6 text-gray-500" />;
  //   }
  // };

  const getExtention = (fileName) =>{
    return fileName.split('.').pop().toLowerCase();
  }
  

  const getFileIcon = (fileName) => {
    const extension = fileName.split('.').pop().toLowerCase();
  
    switch (extension) {
      case 'pdf':
        return <FileText className="w-6 h-6 text-red-500" />;
      case 'jpg':
      case 'jpeg':
      case 'png':
      case 'gif':
        return <FileImage className="w-6 h-6 text-blue-500" />;
      case 'zip':
      case 'rar':
      case '7z':
        return <FileArchive className="w-6 h-6 text-yellow-500" />;
      case 'docx':
      case 'doc':
        return <FileText className="w-6 h-6 text-blue-700" />;
      case 'xlsx':
      case 'xls':
        return <FileText className="w-6 h-6 text-green-600" />;
      case 'pptx':
      case 'ppt':
        return <FileText className="w-6 h-6 text-orange-500" />;
      case 'txt':
        return <FileText className="w-6 h-6 text-gray-500" />;
      case 'mp3':
      case 'wav':
        return <FileText className="w-6 h-6 text-purple-500" />;
      default:
        return <FileText className="w-6 h-6 text-gray-400" />;
    }
  };
  

  const filteredDocuments = documents
    .filter(doc => {
      const matchesSearch = doc.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                          doc.description?.toLowerCase().includes(searchQuery.toLowerCase());
      const matchesCategory = selectedCategory === 'all' || doc.category === selectedCategory;
      return matchesSearch && matchesCategory;
    })
    .sort((a, b) => {
      const order = sortOrder === 'asc' ? 1 : -1;
      switch (sortBy) {
        case 'title':
          return order * a.title.localeCompare(b.title);
        case 'size':
          return order * (a.size - b.size);
        case 'date':
        default:
          return order * (new Date(a.created_at) - new Date(b.created_at));
      }
    });

  return (
    <AuthenticatedLayout>
      <Head title="Documents" />

      <div className="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {/* En-tête */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Documents</h1>
            <p className="mt-1 text-sm text-gray-500">
              {filteredDocuments.length} document(s) disponible(s)
            </p>
          </div>
          <a
            href={route('admin.reports.create')}
            className="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 transition-colors"
          >
            <Plus className="w-5 h-5 mr-2" />
            Ajouter un document
          </a>
        </div>

        {/* Barre de recherche et filtres */}
        <div className="bg-white rounded-lg shadow-sm p-4 mb-6">
          <div className="flex flex-col sm:flex-row gap-4">
            <div className="flex-1 relative">
              <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                type="text"
                placeholder="Rechercher un document..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
              />
            </div>
            <div className="flex gap-2">
              <button
                onClick={() => setShowFilters(!showFilters)}
                className="px-4 py-2 border border-gray-300 rounded-lg flex items-center hover:bg-gray-50"
              >
                <Filter className="w-5 h-5 mr-2" />
                Filtres
                <ChevronDown className={`w-4 h-4 ml-2 transform transition-transform ${showFilters ? 'rotate-180' : ''}`} />
              </button>
              <button
                onClick={() => setSortOrder(sortOrder === 'asc' ? 'desc' : 'asc')}
                className="px-4 py-2 border border-gray-300 rounded-lg flex items-center hover:bg-gray-50"
              >
                {sortOrder === 'asc' ? (
                  <SortAsc className="w-5 h-5" />
                ) : (
                  <SortDesc className="w-5 h-5" />
                )}
              </button>
            </div>
          </div>

          {/* Panneau de filtres */}
          {showFilters && (
            <div className="mt-4 pt-4 border-t border-gray-200">
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Catégorie
                  </label>
                  <select
                    value={selectedCategory}
                    onChange={(e) => setSelectedCategory(e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                  >
                    {categories.map(category => (
                      <option key={category.id} value={category.id}>
                        {category.label}
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">
                    Trier par
                  </label>
                  <select
                    value={sortBy}
                    onChange={(e) => setSortBy(e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                  >
                    <option value="date">Date</option>
                    <option value="title">Titre</option>
                    <option value="size">Taille</option>
                  </select>
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Liste des documents */}
        <div className="bg-white rounded-lg shadow-sm divide-y divide-gray-200">
          {filteredDocuments.length === 0 ? (
            <div className="p-6 text-center">
              <FileText className="w-12 h-12 mx-auto text-gray-400 mb-4" />
              <h3 className="text-lg font-medium text-gray-900 mb-1">
                Aucun document trouvé
              </h3>
              <p className="text-gray-500">
                Essayez de modifier vos critères de recherche
              </p>
            </div>
          ) : (
            filteredDocuments.map((doc) => (
              <div key={doc.id} className="p-4 hover:bg-gray-50 transition-colors">
                <div className="flex items-start justify-between">
                  <div className="flex items-start space-x-4">
                    <div className={`${doc.description ? 'w-10' : 'w-10'} "h-w-1/5"`}>
                    <FileIcon id={doc.id*30} extension={getExtention(doc.file_path)} {...defaultStyles[getExtention(doc.file_path)]}  className="" />
                    </div>
                    <div className='w-4/5'>
                      <h3 className="text-lg font-medium text-gray-900">
                        {doc.title}
                      </h3>
                      {doc.description && (
                        <p className="mt-1 text-sm text-gray-500 line-clamp-1">
                          {doc.description}
                        </p>
                      )}
                      <div className="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <span className="flex items-center">
                          <Calendar className="w-4 h-4 mr-1" />
                          {new Date(doc.created_at).toLocaleDateString()}
                        </span>
                        <span className="flex items-center font-bold uppercase">
                          {/* <User className="w-4 h-4 mr-1" /> */}
                          {getExtention(doc.file_path)}
                        </span>
                        {doc.category && (
                          <span className="flex items-center">
                            <Tag className="w-4 h-4 mr-1" />
                            {doc.category}
                          </span>
                        )}
                      </div>
                    </div>
                  </div>
                  <div className="flex items-center space-x-2">
                    <a
                      href={'/storage/'+doc.file_path}
                      download
                      title="Télécharger"
                      className="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full"
                    >
                      <Download className="w-5 h-5" />
                    </a>
                    <a href={route('admin.reports.edit', {id: doc.id})}
                      title="Modifier"
                      className="p-2 text-gray-400 hover:text-gray-500 hover:bg-gray-100 rounded-full"
                    >
                      <Edit className="w-5 h-5" />
                    </a>
                    <button
                      title="Supprimer"
                      className="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full"
                    >
                      <Trash2 className="w-5 h-5" />
                    </button>
                  </div>
                </div>
              </div>
            ))
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
