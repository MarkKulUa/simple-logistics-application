import { useAuth } from '../contexts/AuthContext';
import { Navigate, useLocation } from 'react-router-dom';
import { toast } from 'react-toastify';

let notified = false;

const ProtectedRoute = ({ children }) => {
    const { user, loading } = useAuth();
    const location = useLocation();

    if (loading) return null;

    if (!user) {
        if (!notified) {
            toast.warning('Access denied. Please log in.');
            notified = true;
            setTimeout(() => (notified = false), 3000);
        }

        return <Navigate to="/login" replace state={{ from: location }} />;
    }

    return children;
};

export default ProtectedRoute;
