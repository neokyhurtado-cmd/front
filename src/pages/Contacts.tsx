import React from 'react';
import ContactCard from '../components/crm/ContactCard';
import ContactForm from '../components/crm/ContactForm';
import { useContacts } from '../hooks/useContacts';

const Contacts = () => {
    const { contacts, addContact } = useContacts();

    const handleAddContact = (newContact) => {
        addContact(newContact);
    };

    return (
        <div>
            <h1>Contacts</h1>
            <ContactForm onAddContact={handleAddContact} />
            <div>
                {contacts.map(contact => (
                    <ContactCard key={contact.id} contact={contact} />
                ))}
            </div>
        </div>
    );
};

export default Contacts;