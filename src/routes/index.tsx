import { createBrowserRouter } from 'react-router-dom';
import Dashboard from '../pages/Dashboard';
import Contacts from '../pages/Contacts';
import ContactDetail from '../pages/ContactDetail';
import Deals from '../pages/Deals';
import Settings from '../pages/Settings';
import DashboardLayout from '../layouts/DashboardLayout';

const router = createBrowserRouter([
  {
    path: '/',
    element: <DashboardLayout />,
    children: [
      { index: true, element: <Dashboard /> },
      { path: 'contacts', element: <Contacts /> },
      { path: 'contacts/:id', element: <ContactDetail /> },
      { path: 'deals', element: <Deals /> },
      { path: 'settings', element: <Settings /> },
    ],
  },
]);

export default router;