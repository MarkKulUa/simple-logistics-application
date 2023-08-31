import { createContext, useContext, useState, useEffect } from 'react';
import axios from '../axios';

const AuthContent = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(
        JSON.parse(localStorage.getItem('user')) || null
    );

    useEffect(() => {
        if (user) {
            localStorage.setItem('user', JSON.stringify(user));
        } else {
            localStorage.removeItem('user');
        }
    }, [user]);

    // csrf token generation for guest methods
    // const csrfToken = async () => {
    //     await axios.get('http://localhost:8000/sanctum/csrf-cookie');
    //     return true;
    // };

    return (
        <AuthContent.Provider value={{ user, setUser }}>
            {children}
        </AuthContent.Provider>
    );
};

export const useAuth = () => {
    return useContext(AuthContent);
};
