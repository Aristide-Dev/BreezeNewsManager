import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputError from '@/Components/InputError';
import { router, usePage } from '@inertiajs/react';
import { 
  Upload, File, FileText, Image, Video, Music,
  Calendar, Tag, X, Plus, Save, AlertCircle
} from 'lucide-react';

export default function DocumentForm({ report = null, categories }) {
    const [dragActive, setDragActive] = useState(false);
    const [preview, setPreview] = useState(null);
    const { errors } = usePage().props

    const { data, setData, post, put, processing } = useForm({
        title: report?.title || '',
        description: report?.description || '',
        category: report?.category || '',
        type: report?.type || 'document', // document, image, video, audio
        tags: report?.tags || [],
        file: null,
        published_at: report?.published_at || '',
        visibility: report?.visibility || 'public',
        language: report?.language || 'fr',
    });

    const fileTypes = {
        document: {
            icon: FileText,
            accept: '.pdf,.doc,.docx,.docx,.xls,.xlsx,.ppt,.pptx',
            label: 'Documents (PDF, Word, Excel, PowerPoint)',
        },
        // image: {
        //     icon: Image,
        //     accept: '.jpg,.jpeg,.png,.gif,.webp',
        //     label: 'Images (JPG, PNG, GIF)',
        // },
        // video: {
        //     icon: Video,
        //     accept: '.mp4,.webm,.avi,.mov',
        //     label: 'Vidéos (MP4, WebM, AVI)',
        // },
        // audio: {
        //     icon: Music,
        //     accept: '.mp3,.wav,.ogg',
        //     label: 'Audio (MP3, WAV, OGG)',
        // },
    };

    // const categories = [
    //     'Rapports Annuels',
    //     'Présentations',
    //     'Documents Techniques',
    //     'Médias',
    //     'Ressources',
    // ];

    const languages = [
        { code: 'fr', label: 'Français' },
        { code: 'en', label: 'English' },
        { code: 'ar', label: 'العربية' },
    ];

    const handleDrag = (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.type === 'dragenter' || e.type === 'dragover') {
            setDragActive(true);
        } else if (e.type === 'dragleave') {
            setDragActive(false);
        }
    };

    const handleDrop = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);
        
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    };

    const handleFile = (file) => {
        setData('file', file);
        
        // Créer une prévisualisation pour les images
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreview(reader.result);
            };
            reader.readAsDataURL(file);
        } else {
            setPreview(null);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();
        
        Object.keys(data).forEach(key => {
            if (key === 'tags' && Array.isArray(data[key])) {
                formData.append(key, JSON.stringify(data[key]));
            } else if (data[key] !== null) {
                formData.append(key, data[key]);
            }
        });

        if (report) {
            formData.append('_method', 'put');
            router.post(route('admin.reports.update', report.id), formData);
        } else {
            post(route('admin.reports.store'), formData);
        }
    };

    return (
        <AuthenticatedLayout>
            <div className="max-w-5xl mx-auto py-8 px-4">
                <div className="bg-white rounded-xl shadow-sm p-6">
                    <div className="mb-8">
                        <h1 className="text-2xl font-bold text-gray-900">
                            {report ? 'Modifier le document' : 'Ajouter un document'}
                        </h1>
                        <p className="text-gray-500 mt-1">
                            Remplissez les informations ci-dessous pour {report ? 'modifier' : 'ajouter'} votre document
                        </p>
                    </div>

                    <form onSubmit={handleSubmit} className="space-y-6" encType="multipart/form-data">
                        {/* Type de fichier */}
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                            {Object.entries(fileTypes).map(([type, { icon: Icon, label }]) => (
                                <button
                                    key={type}
                                    type="button"
                                    onClick={() => setData('type', type)}
                                    className={`p-4 rounded-xl border-2 text-center transition-all ${
                                        data.type === type
                                            ? 'border-primary bg-primary/5'
                                            : 'border-gray-200 hover:border-gray-300'
                                    }`}
                                >
                                    <Icon className={`w-8 h-8 mx-auto mb-2 ${
                                        data.type === type ? 'text-primary' : 'text-gray-400'
                                    }`} />
                                    <span className={`text-sm font-medium ${
                                        data.type === type ? 'text-primary' : 'text-gray-600'
                                    }`}>
                                        {label}
                                    </span>
                                </button>
                            ))}
                        </div>

                        {/* Zone de dépôt de fichier */}
                        <div
                            onDragEnter={handleDrag}
                            onDragLeave={handleDrag}
                            onDragOver={handleDrag}
                            onDrop={handleDrop}
                            className={`relative border-2 border-dashed rounded-xl p-8 text-center transition-all ${
                                dragActive
                                    ? 'border-primary bg-primary/5'
                                    : 'border-gray-300 hover:border-gray-400'
                            }`}
                        >
                            <input
                                type="file"
                                onChange={(e) => handleFile(e.target.files[0])}
                                accept={fileTypes[data.type].accept}
                                className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            />
                            
                            <div className="space-y-4">
                                <Upload className="w-12 h-12 mx-auto text-gray-400" />
                                <div>
                                    <p className="text-lg font-medium text-gray-700">
                                        Glissez-déposez votre fichier ici
                                    </p>
                                    <p className="text-gray-500">
                                        ou cliquez pour sélectionner un fichier
                                    </p>
                                </div>
                                <p className="text-sm text-gray-400">
                                    {fileTypes[data.type].label}
                                </p>
                            </div>

                            {data.file && (
                                <div className="mt-4 p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                                    <div className="flex items-center">
                                        <File className="w-5 h-5 text-gray-400 mr-2" />
                                        <span className="text-sm text-gray-600">
                                            {data.file.name}
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={() => setData('file', null)}
                                        className="p-1 hover:bg-gray-200 rounded-full"
                                    >
                                        <X className="w-4 h-4 text-gray-500" />
                                    </button>
                                </div>
                            )}
                        </div>
                        <InputError message={errors.file} className="mt-2" />

                        {/* Prévisualisation pour les images */}
                        {preview && (
                            <>
                                <div className="relative">
                                <img
                                    src={preview}
                                    alt="Prévisualisation"
                                    className="max-h-64 rounded-lg mx-auto"
                                />
                                <button
                                    type="button"
                                    onClick={() => setPreview(null)}
                                    className="absolute top-2 right-2 p-1 bg-white rounded-full shadow-sm hover:bg-gray-100"
                                >
                                    <X className="w-4 h-4" />
                                </button>
                            </div>
                            <InputError message={errors.file} className="mt-2" />
                            </>
                        )}

                        {/* Informations du document */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Titre
                                </label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                    placeholder="Titre du document"
                                />
                            <InputError message={errors.title} className="mt-2" />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Catégorie
                                </label>
                                <select
                                    name='category'
                                    value={data.category}
                                    onChange={(e) => setData('category', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                >
                                    <option value="">Sélectionner une catégorie</option>
                                    {categories.map(category => (
                                        <option key={category.id} value={category.id}>
                                            {category.label}
                                        </option>
                                    ))}
                                </select>
                                <InputError message={errors.category} className="mt-2" />
                            </div>

                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <textarea
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    rows={4}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                    placeholder="Description du document"
                                />
                                <InputError message={errors.description} className="mt-2" />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Date de publication
                                </label>
                                <input
                                    type="date"
                                    value={data.published_at}
                                    onChange={(e) => setData('published_at', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                />
                                <InputError message={errors.published_at} className="mt-2" />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Langue
                                </label>
                                <select
                                    value={data.language}
                                    onChange={(e) => setData('language', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                >
                                    {languages.map(({ code, label }) => (
                                        <option key={code} value={code}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div className="md:col-span-2">
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Visibilité
                                </label>
                                <div className="flex space-x-4">
                                    {/* {['public', 'private', 'protected'].map((visibility) => ( */}
                                    {['public'].map((visibility) => (
                                        <label
                                            key={visibility}
                                            className="flex items-center space-x-2"
                                        >
                                            <input
                                                type="radio"
                                                checked={data.visibility === visibility}
                                                onChange={() => setData('visibility', visibility)}
                                                className="text-primary focus:ring-primary"
                                            />
                                            <span className="text-sm text-gray-700 capitalize">
                                                {visibility}
                                            </span>
                                        </label>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="flex justify-end space-x-4 pt-6 border-t">
                            <button
                                type="button"
                                onClick={() => window.history.back()}
                                className="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50"
                            >
                                {processing ? (
                                    <span className="flex items-center">
                                        <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Traitement en cours...
                                    </span>
                                ) : (
                                    <span className="flex items-center">
                                        <Save className="w-5 h-5 mr-2" />
                                        {report ? 'Mettre à jour' : 'Enregistrer'}
                                    </span>
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
