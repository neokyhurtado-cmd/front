import React from 'react';

const DealList: React.FC = () => {
    const deals = [
        { id: 1, name: 'Deal 1', amount: '$1000', status: 'Closed' },
        { id: 2, name: 'Deal 2', amount: '$2000', status: 'Open' },
        { id: 3, name: 'Deal 3', amount: '$1500', status: 'Pending' },
    ];

    return (
        <div>
            <h2>Deal List</h2>
            <ul>
                {deals.map(deal => (
                    <li key={deal.id}>
                        <h3>{deal.name}</h3>
                        <p>Amount: {deal.amount}</p>
                        <p>Status: {deal.status}</p>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default DealList;