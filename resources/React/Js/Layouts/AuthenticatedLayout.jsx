import React, { useEffect, useState, useCallback } from 'react';
import { Link, usePage } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { ArrowUp, X, Bell, Menu } from 'lucide-react';

export default function AuthenticatedLayout({ header, children }) {
    const { auth: { user }, flash } = usePage().props;

    // État centralisé
    const [state, setState] = useState({
        showingNavigationDropdown: false,
        showFlash: !!flash?.success || !!flash?.error,
        showBackToTop: false,
        notifications: [],
    });

    // Fonction utilitaire pour mettre à jour l'état
    const updateState = useCallback((updates) => {
        setState(prev => ({ ...prev, ...updates }));
    }, []);

    // Navigation items configuration
    const navigationItems = [
        { name: 'Tableau de Bord', route: 'dashboard', active: route().current('dashboard') },
        { name: 'Actualités', route: 'admin.news.index', active: route().current('admin.news.*')  },
        { name: 'Medias', route: 'admin.medias.index', active: route().current('admin.medias.*')  },
        { name: 'Documents', route: 'admin.reports.index', active: route().current('admin.reports.*')  },
        // { name: 'Utilisateurs', route: 'admin.users.index', active: route().current('admin.users.*')  }
    ];
    if (user.role === 'admin') {
        navigationItems.push({ name: 'Utilisateurs', route: 'admin.users.index', active: route().current('admin.users.*')  });
    }

    // Gestion des messages flash
    useEffect(() => {
        if (flash?.success || flash?.error) {
            updateState({ showFlash: true });
            const timer = setTimeout(() => updateState({ showFlash: false }), 5000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    // Gestion du scroll
    useEffect(() => {
        const handleScroll = () => {
            updateState({ showBackToTop: window.scrollY > 400 });
        };

        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    // Rendu des liens de navigation
    const renderNavLinks = useCallback((responsive = false) => {
        const LinkComponent = responsive ? ResponsiveNavLink : NavLink;
        
        return navigationItems.map(item => (
            <LinkComponent
                key={item.route}
                href={route(item.route)}
                active={item.active}
            >
                {item.name}
            </LinkComponent>
        ));
    }, []);

    // Rendu du message flash
    const renderFlashMessage = useCallback(() => {
        const message = flash?.success || flash?.error;
        const type = flash?.success ? 'success' : 'error';

        if (!state.showFlash || !message) return null;

        return (
            <div className="fixed top-0 left-0 right-0 z-50 animate-slideDown w-full">
                <div className={`max-w-md mx-auto m-4 p-4 rounded-lg shadow-lg ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                } text-white`}>
                    <div className="flex items-center justify-between">
                        <p>{message}</p>
                        <button
                            onClick={() => updateState({ showFlash: false })}
                            className="p-1 hover:bg-white/20 rounded-full transition-colors"
                        >
                            <X className="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        );
    }, [flash, state.showFlash]);

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b border-gray-100">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        {/* Logo et Navigation Desktop */}
                        <div className="flex">
                            <div className="flex items-center shrink-0 bg-primary px-3">
                                <Link href="/">
                                    <ApplicationLogo className="block h-9 w-auto fill-current" />
                                </Link>
                            </div>

                            <div className="hidden sm:flex sm:items-center sm:ms-10 space-x-8">
                                {renderNavLinks()}
                            </div>
                        </div>

                        {/* Menu Utilisateur Desktop */}
                        <div className="hidden sm:flex sm:items-center sm:ms-6">
                            <div className="ms-3 relative">
                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <span className="inline-flex rounded-md">
                                            <button className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                {user.name}
                                                <svg className="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </Dropdown.Trigger>

                                    <Dropdown.Content>
                                        <Dropdown.Link href={route('profile.edit')}>Profile</Dropdown.Link>
                                        <Dropdown.Link href={route('logout')} method="post" as="button">Log Out</Dropdown.Link>
                                    </Dropdown.Content>
                                </Dropdown>
                            </div>
                        </div>

                        {/* Bouton Menu Mobile */}
                        <div className="flex items-center -me-2 sm:hidden">
                            <button
                                onClick={() => updateState({ showingNavigationDropdown: !state.showingNavigationDropdown })}
                                className="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                            >
                                <Menu className={!state.showingNavigationDropdown ? 'block' : 'hidden'} />
                                <X className={state.showingNavigationDropdown ? 'block' : 'hidden'} />
                            </button>
                        </div>
                    </div>
                </div>

                {/* Menu Mobile */}
                <div className={`${state.showingNavigationDropdown ? 'block' : 'hidden'} sm:hidden`}>
                    <div className="pt-2 pb-3 space-y-1">
                        {renderNavLinks(true)}
                    </div>

                    <div className="pt-4 pb-1 border border-gray-500">
                        <div className="px-4">
                            <div className="font-medium text-base text-gray-800">{user.name}</div>
                            <div className="font-medium text-sm text-gray-500">{user.email}</div>
                        </div>

                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink href={route('profile.edit')}>Profile</ResponsiveNavLink>
                            <ResponsiveNavLink method="post" href={route('logout')} as="button">Log Out</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Header */}
            {header && (
                <header className="bg-white shadow">
                    <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            {/* Flash Messages */}
            {renderFlashMessage()}

            {/* Contenu Principal */}
            <main>{children}</main>

            {/* Bouton Retour en Haut */}
            {state.showBackToTop && (
                <button
                    onClick={() => window.scrollTo({ top: 0, behavior: 'smooth' })}
                    className="fixed right-8 bottom-8 p-3 bg-primary text-white rounded-full shadow-lg transition-all duration-300"
                >
                    <ArrowUp className="w-6 h-6" />
                </button>
            )}
        </div>
    );
}
