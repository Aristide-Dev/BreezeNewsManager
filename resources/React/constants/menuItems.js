import { 
  Home,
  Newspaper,
  Building2,
  FileText,
  Link,
  Mail,
  MessageSquare,
  Image,
  Users,
  Briefcase,
  PieChart,
  Boxes,
  Coins,
  Binary,
  FileBarChart
} from 'lucide-react';

export const menuItems = [
  {
    label: 'Accueil',
    href: 'welcome', // route('welcome')
    icon: Home,
  },
  {
    label: 'À Propos',
    icon: Briefcase,
    actif: 'about.*', // route('apropos.index')
    children: [
      { label: 'Présentation', href: 'about.index', icon: FileText }, // route('apropos.presentation')
      { label: 'Organisation', href: 'about.organisation', icon: Users }, // route('about.equipe')
      { label: 'Mission et Objectifs', href: 'about.missions', icon: Briefcase }, // route('about.missions')
      { label: 'Mot du Coordinateur', href: 'about.mot_Coordinateur', icon: MessageSquare }, // route('apropos.coordinateur')
    ]
  },
  {
    label: 'Projets',
    icon: Building2,
    actif: 'directions.*', // route('directions.index')
    children: [
      { label: 'Présentation', href: 'directions.projets', icon: Building2 }, // route('directions.projets')
      { label: 'Ressources Fiscales', href: 'directions.fiscales', icon: PieChart }, // route('directions.fiscales')
      { label: 'Ressources Douanières', href: 'directions.douanieres', icon: Boxes }, // route('directions.douanieres')
      { label: 'Ressources Non Fiscales', href: 'directions.non_fiscales', icon: Coins }, // route('directions.ressources_non_fiscales')
      { label: 'Dépenses et Arriérés', href: 'directions.depenses', icon: FileBarChart }, // route('directions.depenses')
      { label: 'Digitalisation', href: 'directions.digitalisation', icon: Binary }, // route('directions.digitalisation')
    ]
  },
  {
    label: 'Actualité',
    icon: Newspaper,
    actif: 'actualites.*', // route('actualites.index')
    children: [
      { label: 'Actualités', href: 'actualites.index', icon: MessageSquare }, // route('actualites.articles')
      { label: 'Rapports et Publications', href: 'actualites.rapports', icon: FileText }, // route('actualites.rapports')
      { label: 'Médias', href: 'actualites.medias', icon: Image }, // route('actualites.medias')
    ]
  },
  // {
  //   label: 'Ressources',
  //   icon: FileText,
  //   actif: 'ressources.*', // route('ressources.index')
  //   children: [
  //     { label: 'Archives', href: 'ressources.archives', icon: FileText }, // route('ressources.archives')
  //     { label: 'Notes Techniques', href: 'ressources.notes', icon: FileBarChart }, // route('ressources.notes')
  //     { label: 'Rapports Annuels', href: 'ressources.rapports', icon: PieChart }, // route('ressources.rapports')
  //   ]
  // },
  {
    label: 'Partenaires',
    icon: Link,
    href: 'partenariats.index', // route('partenariats.index')
  },
  {
    label: 'Contact',
    icon: Mail,
    href: 'contact.index', // route('contact.index')
  },
];
