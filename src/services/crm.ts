import axios from 'axios';

const API_URL = 'https://api.example.com/crm'; // Replace with your actual API URL

export const getContacts = async () => {
    try {
        const response = await axios.get(`${API_URL}/contacts`);
        return response.data;
    } catch (error) {
        throw new Error('Error fetching contacts');
    }
};

export const addContact = async (contact) => {
    try {
        const response = await axios.post(`${API_URL}/contacts`, contact);
        return response.data;
    } catch (error) {
        throw new Error('Error adding contact');
    }
};

export const updateContact = async (id, contact) => {
    try {
        const response = await axios.put(`${API_URL}/contacts/${id}`, contact);
        return response.data;
    } catch (error) {
        throw new Error('Error updating contact');
    }
};

export const deleteContact = async (id) => {
    try {
        await axios.delete(`${API_URL}/contacts/${id}`);
    } catch (error) {
        throw new Error('Error deleting contact');
    }
};