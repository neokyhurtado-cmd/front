import React from 'react';

const DashboardLayout: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    return (
        <div className="dashboard-layout">
            <header>
                <h1>Dashboard</h1>
                {/* Add navigation or other header elements here */}
            </header>
            <main>
                {children}
            </main>
            <footer>
                {/* Add footer elements here */}
                <p>© 2023 Your Company</p>
            </footer>
        </div>
    );
};

export default DashboardLayout;