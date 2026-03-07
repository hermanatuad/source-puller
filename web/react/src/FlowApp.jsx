import React from "react"
import ReactFlow from "reactflow"
import "reactflow/dist/style.css"

const nodes = [
  {
    id: "1",
    position: { x: 100, y: 100 },
    data: { label: "Node 1" }
  },
  {
    id: "2",
    position: { x: 300, y: 100 },
    data: { label: "Node 2" }
  }
]

const edges = [
  {
    id: "e1-2",
    source: "1",
    target: "2"
  }
]

export default function FlowApp() {
  return (
    <div style={{ width: "100%", height: "600px" }}>
      <ReactFlow nodes={nodes} edges={edges} />
    </div>
  )
}