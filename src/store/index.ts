import { createContext, useContext, useReducer } from 'react';

const initialState = {
  contacts: [],
  deals: [],
  // Add other initial state properties as needed
};

const CRMContext = createContext(initialState);

const crmReducer = (state, action) => {
  switch (action.type) {
    case 'ADD_CONTACT':
      return { ...state, contacts: [...state.contacts, action.payload] };
    case 'REMOVE_CONTACT':
      return { ...state, contacts: state.contacts.filter(contact => contact.id !== action.payload) };
    case 'ADD_DEAL':
      return { ...state, deals: [...state.deals, action.payload] };
    case 'REMOVE_DEAL':
      return { ...state, deals: state.deals.filter(deal => deal.id !== action.payload) };
    default:
      return state;
  }
};

export const CRMProvider = ({ children }) => {
  const [state, dispatch] = useReducer(crmReducer, initialState);

  return (
    <CRMContext.Provider value={{ state, dispatch }}>
      {children}
    </CRMContext.Provider>
  );
};

export const useCRM = () => {
  return useContext(CRMContext);
};