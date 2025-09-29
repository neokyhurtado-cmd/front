"""
Módulo para integración con Google Distance Matrix API
"""

import requests
import logging
from typing import List, Dict, Any, Tuple, Optional
import time

logger = logging.getLogger(__name__)

class GoogleMatrixClient:
    """
    Cliente para Google Distance Matrix API
    """

    BASE_URL = "https://maps.googleapis.com/maps/api/distancematrix/json"

    def __init__(self, api_key: str):
        self.api_key = api_key
        self.session = requests.Session()

    def get_distance_matrix(self,
                           origins: List[str],
                           destinations: List[str],
                           departure_time: str = "now",
                           traffic_model: str = "best_guess") -> Dict[str, Any]:
        """
        Obtiene matriz de distancias entre orígenes y destinos

        Args:
            origins: Lista de direcciones de origen
            destinations: Lista de direcciones de destino
            departure_time: Hora de salida ("now" o timestamp)
            traffic_model: Modelo de tráfico ("best_guess", "pessimistic", "optimistic")

        Returns:
            Respuesta de la API en formato JSON
        """
        params = {
            'origins': '|'.join(origins),
            'destinations': '|'.join(destinations),
            'key': self.api_key,
            'departure_time': departure_time,
            'traffic_model': traffic_model,
            'units': 'metric'
        }

        try:
            response = self.session.get(self.BASE_URL, params=params, timeout=30)
            response.raise_for_status()

            data = response.json()

            if data.get('status') != 'OK':
                logger.error(f"Google Matrix API error: {data.get('status')}")
                return {'error': data.get('status'), 'message': data.get('error_message', 'Unknown error')}

            return data

        except requests.RequestException as e:
            logger.error(f"Request error: {e}")
            return {'error': 'REQUEST_ERROR', 'message': str(e)}
        except Exception as e:
            logger.error(f"Unexpected error: {e}")
            return {'error': 'UNEXPECTED_ERROR', 'message': str(e)}

    def process_od_pairs(self, od_pairs: List[Tuple[str, str]]) -> List[Dict[str, Any]]:
        """
        Procesa lista de pares OD y devuelve resultados

        Args:
            od_pairs: Lista de tuplas (origin, destination)

        Returns:
            Lista de resultados con distancia, tiempo, etc.
        """
        results = []

        # Procesar en batches de 10 para no exceder límites de API
        batch_size = 10

        for i in range(0, len(od_pairs), batch_size):
            batch = od_pairs[i:i + batch_size]
            origins = [pair[0] for pair in batch]
            destinations = [pair[1] for pair in batch]

            matrix_data = self.get_distance_matrix(origins, destinations)

            if 'error' in matrix_data:
                logger.error(f"Batch error: {matrix_data}")
                continue

            # Procesar resultados
            for row_idx, row in enumerate(matrix_data.get('rows', [])):
                for elem_idx, element in enumerate(row.get('elements', [])):
                    origin = origins[row_idx]
                    destination = destinations[elem_idx]

                    result = {
                        'origin': origin,
                        'destination': destination,
                        'status': element.get('status')
                    }

                    if element.get('status') == 'OK':
                        distance_info = element.get('distance', {})
                        duration_info = element.get('duration', {})
                        duration_in_traffic = element.get('duration_in_traffic', {})

                        result.update({
                            'distance_meters': distance_info.get('value'),
                            'distance_text': distance_info.get('text'),
                            'duration_seconds': duration_info.get('value'),
                            'duration_text': duration_info.get('text'),
                            'duration_traffic_seconds': duration_in_traffic.get('value'),
                            'duration_traffic_text': duration_in_traffic.get('text')
                        })

                    results.append(result)

            # Rate limiting: esperar 1 segundo entre batches
            if i + batch_size < len(od_pairs):
                time.sleep(1)

        return results

def load_api_key() -> Optional[str]:
    """
    Carga la API key desde st.secrets o variables de entorno
    """
    try:
        import streamlit as st
        if hasattr(st, 'secrets') and 'google' in st.secrets and 'api_key' in st.secrets['google']:
            return st.secrets['google']['api_key']
    except ImportError:
        pass

    # Fallback a variable de entorno
    import os
    return os.getenv('GOOGLE_MATRIX_API_KEY')