import React, { useState, useCallback, useMemo } from "react";
import { useForm, Head, router } from "@inertiajs/react";
import InputError from '@/Components/InputError';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Upload, Link, Image, Video, FileText, Clock, Calendar, X } from 'lucide-react';

export default function MediaForm({ media = null }) {
    const isExternalUrl = useCallback((url) => {
        return url?.startsWith('http://') || url?.startsWith('https://') || url?.startsWith('www');
    }, []);

    const [isFileUpload, setIsFileUpload] = useState(() => !isExternalUrl(media?.url || ""));
    const [previewUrl, setPreviewUrl] = useState(() =>
        isExternalUrl(media?.url || "") ? media?.url : `/storage/${media?.url}` || null
    );
    const [isNewCategory, setIsNewCategory] = useState(false);

    const formatDate = (dateString) => {
        if (!dateString) return '';
        return new Date(dateString).toISOString().split('T')[0];
    };

    const { data, setData, post, put, progress, processing, errors } = useForm({
        type: media?.type || "image",
        title: media?.title || "",
        url: media?.url || "",
        file: null,
        description: media?.description || "",
        category: media?.category || "",
        embed_url: media?.embed_url || "",
        duration: media?.duration || "",
        published_at: formatDate(media?.published_at) || "",
    });

    const categories = useMemo(() => [
        "Événements officiels",
        "Réunions",
        "Conférences",
        "Formations",
        "Interviews",
        "Reportages",
        "Autres"
    ], []);

    const handleFileChange = useCallback((e) => {
        const file = e.target.files[0];
        if (file) {
            setData("file", file);
            setPreviewUrl(URL.createObjectURL(file));
        }
    }, [setData]);

    const handleSubmit = useCallback((e) => {
        e.preventDefault();
        setData('processing', true);

        // Vérifier si au moins un champ est rempli
        const hasContent = Object.entries(data).some(([key, value]) => {
            if (key === 'type') return false; // Ignorer le type qui est toujours présent
            if (value === null || value === undefined || value === '') return false;
            return true;
        });

        if (!hasContent) {
            alert('Veuillez remplir au moins un champ avant de soumettre le formulaire.');
            return;
        }

        console.log(media? 'put' : 'post');

        const formData = {
            _method: media ? 'put' : 'post', // Forcer la méthode PUT pour l'édition
            type: data.type,
            title: data.title,
            description: data.description,
            category: data.category,
            published_at: data.published_at,
            ...(isFileUpload 
                ? (data.file ? { file: data.file } : {}) 
                : { url: data.url }
            ),
            ...(data.type === 'video' && {
                embed_url: data.embed_url,
                duration: data.duration
            })
        };

        const options = {
            forceFormData: true,
            onSuccess: () => {
                window.location.href = route("admin.medias.index");
            },
        };

        // Toujours utiliser post avec le method spoofing
        router.post(media 
            ? route("admin.medias.update", media.id)
            : route("admin.medias.store"), 
            formData, 
            options
        );
    }, [data, isFileUpload, media, post]);

    // Fonction utilitaire pour obtenir toutes les erreurs uniques
    const getAllErrors = useCallback(() => {
        return Object.values(errors).filter(error => error);
    }, [errors]);

    return (
        <AuthenticatedLayout>
            {/* Loader global */}
            {processing && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-md">
                <div className="flex flex-col items-center">
                  <div className="w-16 h-16 border-4 border-t-transparent border-primary rounded-full animate-spin"></div>
                  <p className="mt-4 text-lg font-semibold text-white animate-pulse">
                    Chargement en cours...
                  </p>
                </div>
              </div>
            )}

            <Head title={media ? "Modifier un média" : "Ajouter un média"} />
            <div className="max-w-4xl mx-auto py-8 px-4">
                <div className="bg-white rounded-xl shadow-lg p-8">
                    {/* Affichage des erreurs en en-tête */}
                    {getAllErrors().length > 0 && (
                        <div className="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div className="font-medium text-red-800 mb-2">
                                Veuillez corriger les erreurs suivantes :
                            </div>
                            <ul className="list-disc list-inside text-sm text-red-600">
                                {getAllErrors().map((error, index) => (
                                    <li key={index}>{error}</li>
                                ))}
                            </ul>
                        </div>
                    )}

                    <header className="border-b pb-6 mb-6">
                        <h1 className="text-3xl font-bold text-gray-900">
                            {media ? "Modifier" : "Ajouter"} un Média
                        </h1>
                        <p className="mt-2 text-gray-600">
                            Les champs marqués d'un  <span className="text-red-500">*</span> sont obligatoires.
                        </p>
                    </header>

                    <form onSubmit={handleSubmit} className="space-y-6" encType="multipart/form-data">
                        {/* Type de média */}
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Type de média  <span className="text-red-500">*</span>
                                </label>
                                <div className="flex space-x-4">
                                    {['image', 'video'].map(type => (
                                        <button
                                            key={type}
                                            type="button"
                                            onClick={() => setData("type", type)}
                                            aria-label={`Type: ${type}`}
                                            className={`flex items-center px-4 py-2 rounded-lg border ${
                                                data.type === type
                                                    ? 'bg-primary text-white border-primary'
                                                    : 'border-gray-300 hover:bg-gray-50'
                                            }`}
                                        >
                                            {type === 'image' ? <Image className="w-5 h-5 mr-2" /> : <Video className="w-5 h-5 mr-2" />}
                                            {type.charAt(0).toUpperCase() + type.slice(1)}
                                        </button>
                                    ))}
                                </div>
                                <InputError message={errors.type} />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Méthode d'ajout
                                </label>
                                <div className="flex space-x-4">
                                    {[
                                        { value: false, label: 'URL', icon: Link },
                                        { value: true, label: 'Fichier', icon: Upload }
                                    ].map(method => (
                                        <button
                                            key={method.label}
                                            type="button"
                                            onClick={() => setIsFileUpload(method.value)}
                                            aria-label={`Méthode: ${method.label}`}
                                            className={`flex items-center px-4 py-2 rounded-lg border ${
                                                isFileUpload === method.value
                                                    ? 'bg-primary text-white border-primary'
                                                    : 'border-gray-300 hover:bg-gray-50'
                                            }`}
                                        >
                                            <method.icon className="w-5 h-5 mr-2" />
                                            {method.label}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Titre et Catégorie */}
                        <div className="grid grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Titre  <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.title}
                                    onChange={(e) => setData("title", e.target.value)}
                                    className="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary"
                                    placeholder="Entrez un titre descriptif"
                                />
                                <InputError message={errors.title} />
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-2">
                                    Catégorie
                                </label>
                                {!isNewCategory ? (
                                    <select
                                        value={data.category}
                                        onChange={(e) => setData("category", e.target.value)}
                                        className="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary"
                                    >
                                        <option value="">Sélectionnez une catégorie</option>
                                        {categories.map(cat => (
                                            <option key={cat} value={cat}>{cat}</option>
                                        ))}
                                        <option value="new">Nouvelle catégorie...</option>
                                    </select>
                                ) : (
                                    <input
                                        type="text"
                                        value={data.category}
                                        onChange={(e) => setData("category", e.target.value)}
                                        className="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="Entrez une nouvelle catégorie"
                                    />
                                )}
                                <InputError message={errors.category} />
                            </div>
                        </div>

                        {/* Zone de téléchargement/URL */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                {isFileUpload ? "Fichier *" : "URL *"}
                            </label>
                            {isFileUpload ? (
                                <div>
                                    <input
                                        type="file"
                                        id="file-upload"
                                        className="hidden"
                                        accept={data.type === 'image' ? 'image/*' : 'video/*'}
                                        onChange={handleFileChange}
                                    />
                                    <label
                                        htmlFor="file-upload"
                                        className="flex items-center justify-center space-y-2 cursor-pointer"
                                    >
                                        <Upload className="w-12 h-12 text-gray-400" />
                                        <span className="text-sm text-gray-600">Cliquez pour sélectionner un fichier ou glissez-le ici</span>
                                    </label>
                                    {previewUrl && (
                                        <div className="relative w-full h-48 mt-4">
                                            {data.type === 'image' ? (
                                                <img
                                                    src={previewUrl}
                                                    alt="Aperçu"
                                                    className="w-full h-full object-cover rounded-lg"
                                                />
                                            ) : (
                                                <video
                                                    src={previewUrl}
                                                    controls
                                                    className="w-full h-full object-cover rounded-lg"
                                                />
                                            )}
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    setPreviewUrl(null);
                                                    setData("file", null);
                                                }}
                                                className="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                            >
                                                <X className="w-4 h-4" />
                                            </button>
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <input
                                    type="url"
                                    value={data.url}
                                    onChange={(e) => setData("url", e.target.value)}
                                    className="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary"
                                    placeholder={`Entrez l'URL du ${data.type}`}
                                />
                            )}
                        </div>
                        
                        {/* Date de publication */}
                        <div>
                            <div className="bg-white rounded-xl shadow-sm p-6">
                                <div className="flex items-center mb-4">
                                    <Calendar className="w-5 h-5 text-gray-400 mr-2" />
                                    <label className="text-lg font-medium text-gray-900">
                                        Publication
                                    </label>
                                </div>
                                <input
                                    type="date"
                                    id="published_at"
                                    name="published_at"
                                    value={data.published_at}
                                    onChange={(e) => setData('published_at', e.target.value)}
                                    className="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary"
                                />
                            </div>
                            <InputError message={errors.published_at} className="mt-2" />
                        </div>

                        {/* Description */}
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Description
                                <span className="text-sm text-gray-500 ml-1">(optionnel)</span>
                            </label>
                            <textarea
                                value={data.description}
                                onChange={(e) => setData("description", e.target.value)}
                                rows={4}
                                className="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary"
                                placeholder="Ajoutez une description détaillée"
                            />
                            <InputError message={errors.description} />
                        </div>

                        {/* Options supplémentaires pour les vidéos */}
                        {data.type === 'video' && (
                            <div className="grid grid-cols-2 gap-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        URL d'intégration
                                        <span className="text-sm text-gray-500 ml-1">(optionnel)</span>
                                    </label>
                                    <input
                                        type="url"
                                        value={data.embed_url}
                                        onChange={(e) => setData("embed_url", e.target.value)}
                                        className="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="URL YouTube/Vimeo"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 mb-2">
                                        Durée
                                        <span className="text-sm text-gray-500 ml-1">(optionnel)</span>
                                    </label>
                                    <input
                                        type="number"
                                        value={data.duration}
                                        onChange={(e) => setData("duration", e.target.value)}
                                        className="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary"
                                        placeholder="Durée de la vidéo"
                                    />
                                </div>
                            </div>
                        )}

                        {/* Boutons d'action */}
                        <div className="flex justify-end space-x-4 pt-6 border-t">
                            <button
                                type="button"
                                onClick={() => window.history.back()}
                                className="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-700 disabled:opacity-50"
                            >
                                {processing ? "En cours..." : media ? "Mettre à jour" : "Créer"}
                            </button>
                        </div>

                    </form>

                    {/* Afficher la barre de progression */}
                    {progress && (
                        <div className="mt-2">
                            <div className="w-full bg-gray-200 rounded-full h-2.5">
                                <div 
                                    className="bg-primary h-2.5 rounded-full" 
                                    style={{ width: `${progress.percentage}%` }}
                                />
                            </div>
                            <span className="text-sm text-gray-600">{progress.percentage}%</span>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
