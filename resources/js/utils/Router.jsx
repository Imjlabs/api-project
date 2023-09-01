import React from "react";
import { Routes, Route } from "react-router-dom";
import Register from "../pages/Register";

const Router = () => {
    return (
        <Routes>
            <Route path="/" element={<Register />} />
        </Routes>
    )
}

export default Router