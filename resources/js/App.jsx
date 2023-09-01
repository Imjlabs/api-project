import React from 'react';
import { createRoot } from 'react-dom/client'
import { BrowserRouter } from "react-router-dom"
import Router from './utils/Router';

export default function App(){
    return(
        <Router />
    );
}

if(document.getElementById('root')){
    createRoot(document.getElementById('root')).render(
        <BrowserRouter>
            <React.StrictMode>
                <App/>
            </React.StrictMode>
        </BrowserRouter>
    )
}