const facts = [
  "NGINX se pronuncia como 'engine-x'.",
  "Es usado por más del 30% de los sitios web en Internet.",
  "Puede manejar más de 10,000 conexiones simultáneas con bajo consumo.",
  "Es ideal para arquitecturas modernas como microservicios.",
  "Fue creado originalmente para resolver el problema C10k en Rusia."
];

document.getElementById("fact-btn").addEventListener("click", () => {
  const randomIndex = Math.floor(Math.random() * facts.length);
  document.getElementById("fact-text").textContent = facts[randomIndex];
});
